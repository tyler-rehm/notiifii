<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">Example user</strong>
                            </span> <span class="text-muted text-xs block">Example menu <b class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="#">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            <li class="{{ isActiveRoute('main') }}">
                <a href="{{ url('/') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Home</span></a>
            </li>
            <li class="{{ isActiveRoute('messages') }}">
                <a href="{{ url('/messages') }}"><i class="fa fa-envelope"></i> <span class="nav-label">Messages</span></a>
            </li>
            <li class="{{ isActiveRoute('contacts') }}">
                <a href="{{ url('/contacts') }}"><i class="fa fa-address-book-o"></i> <span class="nav-label">Contacts</span></a>
            </li>
            <li class="{{ isActiveRoute('settings') }}">
                <a href="{{ url('/settings') }}"><i class="fa fa-cogs"></i> <span class="nav-label">Settings</span></a>
            </li>
        </ul>

    </div>
</nav>
