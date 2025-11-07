<div class="nav navShow" id="nav">
    <div class="two">
        <ul class="">
            <li>
                <?= strtoupper($_SESSION['username']) ?? "Hi,"; ?>
            </li>
            <li class="relative" id="logout">

                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" id="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" stroke="#ffffffff" 
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z">
                        </path>
                    </svg>

                    <svg id="downArrow" viewBox="-0.48 -0.48 24.96 24.96" fill="none" xmlns="http://www.w3.org/2000/svg">
                       
                            <path fill=""
                                d="M19 9L14 14.1599C13.7429 14.4323 13.4329 14.6493 13.089 14.7976C12.7451 14.9459 12.3745 15.0225 12 15.0225C11.6255 15.0225 11.2549 14.9459 10.9109 14.7976C10.567 14.6493 10.2571 14.4323 10 14.1599L5 9"
                                stroke="#000000ff" fill="none" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        
                    </svg>
                </div>


            </li>
        </ul>
    </div>
    <div id="accountOption">
        <ul>
            <li><a href="dashboard/logout.php">LogOut</a></li>
            <li><a href="">Change Name</a></li>
            <li><a href="">Setting</a></li>

        </ul>
    </div>
    <hr>
    <div class="one">
        <a class="<?=$page == 'main'? 'active' :'';?> <?=$page == 'reviewSingle'? 'active' :'';?>" href="dashboard.php?page=main">Submission</a>
        <a class="<?=$page == 'user'? 'active' :'';?> <?=$page == 'addUser'? 'active' :'';?>"  href="dashboard.php?page=user&display=all">Users</a>
        <a class="<?=$page == 'layout'? 'active' :'';?>"  href="dashboard.php?page=layout">Layout</a>
    </div>

</div>