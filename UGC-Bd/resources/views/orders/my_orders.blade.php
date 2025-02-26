<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
</head>

<body>
    <h1>My Orders</h1>

    @if ($orders->isEmpty())
    <p>You have no orders.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Start Date</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->service->title }}</td>
                <td>{{ $order->first_name }}</td>
                <td>{{ $order->last_name }}</td>
                <td>{{ $order->phone }}</td>
                <td>{{ $order->start_date }}</td>
                <td>{{ $order->price }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>

</html>
