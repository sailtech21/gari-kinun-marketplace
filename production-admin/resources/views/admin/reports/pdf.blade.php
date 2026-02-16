<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #10b981; color: white; }
        h1 { color: #1f2937; }
    </style>
</head>
<body>
    <h1>Listings Report</h1>
    <p>Generated: {{ date('Y-m-d H:i:s') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Views</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listings as $listing)
            <tr>
                <td>{{ $listing->id }}</td>
                <td>{{ $listing->title }}</td>
                <td>{{ $listing->category->name ?? '' }}</td>
                <td>${{ number_format($listing->price, 2) }}</td>
                <td>{{ $listing->status }}</td>
                <td>{{ $listing->views }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
