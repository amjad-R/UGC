<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Chat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Chat with Support</h1>

        <div class="chat-box border rounded p-3 mb-3" style="height: 300px; overflow-y: scroll;">
            @if($messages->isEmpty())
            <p>No messages yet.</p>
            @else
            @foreach($messages as $message)
            <div class="{{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                <p>
                    <strong>
                        @if($message->sender_id === auth()->id())
                        You
                        @else
                        Support
                        @endif:
                    </strong>
                    {{ $message->content }}
                </p>
            </div>
            @endforeach
            @endif
        </div>

        <form action="{{ route('chat.sendSupportMessage') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" name="message" class="form-control" placeholder="Type your message here..." required>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>
</body>

</html>
