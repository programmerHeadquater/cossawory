<div class="nav">
    <a href="dashboard.php?page=main">All reviews</a>
    <ul>
        <li>
            <p>Hi, <?= $_SESSION['username'] ?> </p>
        </li>
        <li class="relative">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" id="logoutIcon">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>

            <div id="accountOption">
                <ul>
                    <li><a href="dashboard/logout.php">LogOut</a></li>
                    <li><a href="">Change Name</a></li>
                    <li><a href="">Setting</a></li>

                </ul>
            </div>
        </li>
    </ul>
</div>