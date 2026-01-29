<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Issue Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h1 class="display-4 text-primary mb-4">
                            <i class="fas fa-university me-3"></i>
                            Issue Board
                        </h1>
                        
                        <p class="lead mb-4">
                            Welcome to the campus issue reporting system. Report and track campus issues efficiently.
                        </p>

                        <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mt-4">
                                <a href="{{ route('tickets.index') }}" class="btn btn-primary btn-lg px-4 gap-3">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                Tickets
                                </a>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-success btn-lg px-4 gap-3">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        Admin Panel
                            </a>
                            @auth
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-lg px-4 gap-3">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                        @else
                                <button type="button" class="btn btn-danger btn-lg px-4 gap-3" onclick="alert('You are not logged in to any account!')">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            @endauth
                            </div>
                    </div>
                </div>
                </div>
        </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
