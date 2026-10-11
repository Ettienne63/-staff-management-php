                    <nav class="sidebar-nav" aria-label="School navigation">
                        <a href="{{ route('schools.workspace', $currentSchool) }}" @if(request()->routeIs('schools.workspace')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">◫</span><span>Overview</span></a>
                        @if(in_array($schoolRole, \App\Models\User::StaffRoles, true))
                            <a href="{{ route('staff.index', $currentSchool) }}" @if(request()->routeIs('staff.*')) aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">♧</span><span>Staff</span></a>
                        @endif
                        @if(in_array($schoolRole, ['principal', 'deputy', 'coach'], true))
                            <a href="{{ route('schools.section', [$currentSchool, 'students']) }}" @if(request()->route('section') === 'students') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">♙</span><span>Students</span></a>
                        @endif
                        @if(in_array($schoolRole, \App\Models\User::ManagementRoles, true))
                            <a href="{{ route('schools.section', [$currentSchool, 'accounts']) }}" @if(request()->route('section') === 'accounts') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">＋</span><span>Accounts</span></a>
                        @endif
                        @if($schoolRole !== 'hr')
                        <a href="{{ route('schools.section', [$currentSchool, 'training']) }}" @if(request()->route('section') === 'training') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">◷</span><span>Training</span></a>
                        <a href="{{ route('schools.section', [$currentSchool, 'progress']) }}" @if(request()->route('section') === 'progress') aria-current="page" @endif><span class="sidebar-icon" aria-hidden="true">↗</span><span>Progress</span></a>
                        @endif
                    </nav>
