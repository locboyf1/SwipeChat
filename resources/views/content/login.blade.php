<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sign In – Swipe</title>
    <meta name="description" content="#">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('dist/css/lib/bootstrap.min.css') }}" type="text/css" rel="stylesheet">
    <!-- Swipe core CSS -->
    <link href="{{ asset('dist/css/swipe.min.css') }}" type="text/css" rel="stylesheet">
    <!-- Favicon -->
    <link href="{{ asset('dist/img/favicon.png') }}" type="image/png" rel="icon">
</head>

<body class="start">
    <main>
        <div class="layout">
            <!-- Start of Sign In -->
            <div class="main order-md-1">
                <div class="start">
                    <div class="container">
                        <div class="col-md-12">
                            <div class="content">
                                <h1>Đăng nhập vào Swipe</h1>
                                <div class="third-party">
                                    <button class="btn item bg-blue">
                                        <i class="material-icons">pages</i>
                                    </button>
                                    <button class="btn item bg-teal">
                                        <i class="material-icons">party_mode</i>
                                    </button>
                                    <button class="btn item bg-purple">
                                        <i class="material-icons">whatshot</i>
                                    </button>
                                </div>
                                <p>hoặc đăng nhập với email của bạn:</p>
                                <form method="POST" action="{{ route('login.post') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Địa chỉ mail"
                                            name="email">
                                        <button class="btn icon"><i class="material-icons">mail_outline</i></button>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" placeholder="Mật khẩu"
                                            name="password">
                                        <button class="btn icon"><i class="material-icons">lock_outline</i></button>
                                    </div>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li class="text-danger text-left">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="submit" class="btn button">Đăng nhập</button>
                                    <div class="callout">
                                        <span>Không có tài khoản? <a href="{{ route('register.get') }}">Đăng
                                                ký</a></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Sign In -->
            <!-- Start of Sidebar -->
            <div class="aside order-md-2">
                <div class="container">
                    <div class="col-md-12">
                        <div class="preference">
                            <h2>Chào mừng bạn trở lại!</h2>
                            <p>Để giữ kết nối với bạn bè, vui lòng đăng nhập bằng thông tin cá nhân của bạn.</p>
                            <a href="{{ route('register.get') }}" class="btn button">Đăng ký</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Sidebar -->
        </div> <!-- Layout -->
    </main>
    <!-- Bootstrap core JavaScript
  ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ asset('dist/js/jquery-3.3.1.slim.min.js') }}"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script>
        window.jQuery || document.write('<script src="{{ asset('dist/js/vendor/jquery-slim.min.js') }}"><\/script>')
    </script>
    <script src="{{ asset('dist/js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('dist/js/bootstrap.min.js') }}"></script>
</body>

</html>
