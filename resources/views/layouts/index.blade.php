<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        nav {
            background-color: #4A5568;
        }

        .container-content {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .progress-bar {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 5px;
            margin: 15px 0;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 25px;
            background-color: #4caf50;
            width: 0;
            transition: width 0.5s ease-in-out;
        }

        .upload-form {
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            background-color: #4a5568;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:disabled {
            background-color: #a0aec0;
        }

        .error-message {
            color: #e53e3e;
            margin-top: 0.5rem;
        }

        .status-message {
            text-align: center;
            margin-top: 1rem;
        }
    </style>

</head>

<body class="bg-gray-100">

    <!-- Header -->
    <nav class="p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Dashboard</h1>
            <ul class="flex space-x-4">
                <li><a href="{{ route('home') }}" class="hover:underline">Dashboard</a></li>
                <li><a href="{{ route('excel_upload') }}" class="hover:underline">Upload Excel File</a></li>
                <li><a href="{{ route('upload.file') }}" class="hover:underline">Upload File</a></li>
            </ul>
        </div>
    </nav>

    @yield('content')

</body>

</html>
