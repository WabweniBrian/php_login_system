<div class="container">
    <div class="row justify-content-center align-items-center g-2 min-vh-100">
        <div class="col-sm-12 col-md-6 bg-white rounded p-3">
            <h3 class="text-center">REGISTER</h3>
            <form action="" method="post">
                <div class="mt-4">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username.."
                            value="<?= $username ?>">
                        <small class="form-text text-muted text-danger"><?= $errors['username'] ?></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email address.."
                            value="<?= htmlspecialchars($email) ?>">
                        <small class="form-text text-muted text-danger"><?= $errors['email'] ?></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password.."
                            value="<?= htmlspecialchars($password) ?>">
                        <small class="form-text text-muted text-danger"><?= $errors['password'] ?></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Password Confirmation.."
                            value="<?= htmlspecialchars($password_confirmation) ?>">
                        <small class="form-text text-muted text-danger"><?= $errors['password_confirmation'] ?></small>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>
            <div class="mt-3 float-end">
                <p>Already have an account? <a href="login.php" class="text-primary">Login</a></p>
            </div>
        </div>
    </div>
</div>