<!DOCTYPE html>
<html>

<head>
    <title>Import SQL File</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>Import SQL File</h1>

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('backup.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="backup_file">Upload SQL File</label>
                <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".sql,.txt" required>
            </div>
            <div class="form-group">
                <label for="business_identifier">Select Business</label>
                <select name="business_identifier" id="business_identifier" class="form-control" required>
                    <option value="">-- Select Business --</option>
                    @foreach ($businesses as $business)
                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
</body>

</html>