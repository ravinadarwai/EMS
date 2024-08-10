<header class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom " style="background:#e58039;">
                <h1 class="mx-5">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h1>
                <!-- <div class="d-flex">
                    <input type="text" class="form-control me-2" placeholder="Type here to search..." />
                    <button class="btn btn-primary">Search</button>
                </div> -->
                <a href="logout.php" class="btn btn-dark mx-5">Logout</a>
            </header>