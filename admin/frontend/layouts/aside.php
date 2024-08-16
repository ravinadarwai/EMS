<aside class="col-md-3 col-lg-2 bg-dark text-light">
            <header class="d-flex align-items-center p-3">
                <img class="profile-picture" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/584938/people_10.png" alt="Profile Picture"/>
                <p class="ms-3 mb-0"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
            </header>
            <nav class="side-navigation">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_project">Add Projects</a></li>
                    <li class="nav-item"><a class="nav-link" href="show_all_project">Show All Project</a></li>
            
                </ul>
            </nav>
        </aside>
