<label for="public_email">Public school email (optional)</label>
<input id="public_email" name="public_email" type="email" value="{{ old('public_email', $school->public_email ?? '') }}" maxlength="255">
<label for="public_phone">Public school phone (optional)</label>
<input id="public_phone" name="public_phone" type="tel" value="{{ old('public_phone', $school->public_phone ?? '') }}" maxlength="40">
<label for="website">School website (optional)</label>
<input id="website" name="website" type="url" value="{{ old('website', $school->website ?? '') }}" maxlength="255">
<p class="text-sm">Use the school's public contact details. Personal staff and family contact details belong in their private school records.</p>
