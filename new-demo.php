<?php

// Stamps out a new demo app on its own orphan branch, from this template.
//
//   php <template>/new-demo.php <branch> "<App name>" [--dry-run] [--path=<dir>]
//
// Run it from the root of a clone of this repository. It creates the orphan branch in a new
// worktree (default: ../<repo>-<branch>), installs a fresh Laravel there, copies skeleton/
// over it with the placeholders filled in, adds Laravel Boost, and makes the first commit.
// It never pushes, and it never touches the checkout you run it from.

const LARAVEL = 'laravel/laravel:^13';
const BOOST = 'laravel/boost:^2.10';

function fail(string $message): never
{
    fwrite(STDERR, "new-demo: {$message}\n");
    exit(1);
}

function run(string $command, bool $dryRun, ?string $cwd = null): string
{
    $shown = ($cwd ? "(in {$cwd}) " : '').$command;
    echo "  \$ {$shown}\n";
    if ($dryRun) {
        return '';
    }
    $previous = getcwd();
    if ($cwd) {
        chdir($cwd);
    }
    exec($command.' 2>&1', $output, $code);
    chdir($previous);
    $text = implode("\n", $output);
    if ($code !== 0) {
        fail("command failed ({$code}): {$command}\n{$text}");
    }

    return $text;
}

function succeeds(string $command): bool
{
    exec($command.' 2>&1', $output, $code);

    return $code === 0;
}

$args = array_values(array_filter(array_slice($argv, 1), fn (string $a): bool => ! str_starts_with($a, '--')));
$options = array_values(array_filter(array_slice($argv, 1), fn (string $a): bool => str_starts_with($a, '--')));
$dryRun = in_array('--dry-run', $options, true);
$path = null;
foreach ($options as $option) {
    if (str_starts_with($option, '--path=')) {
        $path = substr($option, 7);
    }
}

if (count($args) !== 2) {
    fail('usage: php new-demo.php <branch> "<App name>" [--dry-run] [--path=<dir>]');
}
[$branch, $appName] = $args;
if (! preg_match('/^[a-z0-9][a-z0-9-]*$/', $branch)) {
    fail("branch name must be lower-case kebab-case, got '{$branch}'");
}
if (trim($appName) === '') {
    fail('app name must not be empty');
}

$skeleton = __DIR__.DIRECTORY_SEPARATOR.'skeleton';
if (! is_dir($skeleton)) {
    fail("skeleton/ not found next to this script ({$skeleton})");
}

exec('git rev-parse --show-toplevel 2>&1', $top, $code);
if ($code !== 0) {
    fail('run this from inside a clone of the repository');
}
$root = $top[0];
if (realpath($root) === realpath(__DIR__)) {
    fail('run this from the app repository checkout, not from inside the template worktree');
}
if (succeeds('git -C '.escapeshellarg($root).' show-ref --verify --quiet refs/heads/'.escapeshellarg($branch))) {
    fail("branch '{$branch}' already exists locally");
}
if (succeeds('git -C '.escapeshellarg($root).' ls-remote --exit-code --heads origin '.escapeshellarg($branch))) {
    fail("branch '{$branch}' already exists on origin");
}

$target = $path ?? dirname($root).DIRECTORY_SEPARATOR.basename($root).'-'.$branch;
if (file_exists($target)) {
    fail("target folder already exists: {$target}");
}

$placeholders = [
    '{{APP_NAME}}' => $appName,
    '{{BRANCH}}' => $branch,
    '{{DATE}}' => date('Y-m-d'),
];

echo ($dryRun ? "Dry run: nothing will be changed.\n" : '')."Creating {$appName} on orphan branch {$branch}\n  in {$target}\n\n";

echo "1. Orphan branch in a new worktree\n";
run('git -C '.escapeshellarg($root).' worktree add --orphan -b '.escapeshellarg($branch).' '.escapeshellarg($target), $dryRun);

echo "2. Fresh Laravel (".LARAVEL.")\n";
$scratch = $target.DIRECTORY_SEPARATOR.'.laravel-install';
run('composer create-project '.escapeshellarg(LARAVEL).' '.escapeshellarg($scratch).' --no-interaction --prefer-dist', $dryRun);
echo "  move the install up into the worktree\n";
if (! $dryRun) {
    foreach (scandir($scratch) as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        rename($scratch.DIRECTORY_SEPARATOR.$entry, $target.DIRECTORY_SEPARATOR.$entry) || fail("could not move {$entry}");
    }
    rmdir($scratch) || fail("could not remove {$scratch}");
}

echo "3. Template files from skeleton/\n";
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($skeleton, FilesystemIterator::SKIP_DOTS));
foreach ($files as $file) {
    $relative = substr($file->getPathname(), strlen($skeleton) + 1);
    if ($relative === 'gitignore-append') {
        continue;
    }
    $destination = $target.DIRECTORY_SEPARATOR.$relative;
    echo "  write {$relative}\n";
    if (! $dryRun) {
        is_dir(dirname($destination)) || mkdir(dirname($destination), 0777, true) || fail("could not create folder for {$relative}");
        $content = strtr(file_get_contents($file->getPathname()), $placeholders);
        file_put_contents($destination, $content) !== false || fail("could not write {$relative}");
    }
}
echo "  append gitignore-append to .gitignore\n";
if (! $dryRun) {
    $block = strtr(file_get_contents($skeleton.DIRECTORY_SEPARATOR.'gitignore-append'), $placeholders);
    file_put_contents($target.DIRECTORY_SEPARATOR.'.gitignore', "\n".$block, FILE_APPEND) !== false || fail('could not update .gitignore');
}

echo "4. Laravel Boost (".BOOST.")\n";
run('composer require --dev '.escapeshellarg(BOOST).' --no-interaction', $dryRun, $target);
echo "   and package-lock.json, which CI's npm ci needs\n";
run('npm install --no-audit --no-fund', $dryRun, $target);

echo "5. Check no placeholder was left unfilled\n";
if (! $dryRun) {
    $left = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($skeleton, FilesystemIterator::SKIP_DOTS)) as $file) {
        $relative = substr($file->getPathname(), strlen($skeleton) + 1);
        $written = $relative === 'gitignore-append' ? '.gitignore' : $relative;
        if (preg_match('/\{\{[A-Z_]+\}\}/', (string) file_get_contents($target.DIRECTORY_SEPARATOR.$written))) {
            $left[] = $written;
        }
    }
    $left === [] || fail('unfilled placeholders in: '.implode(', ', $left));
}

echo "6. First commit\n";
run('git add -A', $dryRun, $target);
run('git commit -q -m '.escapeshellarg("chore(template): start {$appName} from the sports app template"), $dryRun, $target);

echo <<<TEXT

Done. Next, in {$target}:
  cp .env.example .env && php artisan key:generate
  php artisan boost:install      # writes the Boost guidelines into CLAUDE.md and AGENTS.md
  git diff CLAUDE.md AGENTS.md   # check the project section at the top survived; restore it if not
  php artisan test && vendor/bin/pint --test
  git add CLAUDE.md AGENTS.md boost.json && git commit -m "chore(boost): add the Laravel Boost guidelines"
  git push -u origin {$branch}   # only when you're ready; CI runs on push to {$branch}

TEXT;
