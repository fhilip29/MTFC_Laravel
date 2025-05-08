<div class="flex items-start space-x-3">
    <img src="{{ $comment->user->profile_image ? asset($comment->user->profile_image) : asset('assets/default-user.png') }}"
         alt="User" class="w-8 h-8 rounded-full object-cover">
    <div class="flex-1">
        <div class="bg-[#1a1a1a] p-3 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center">
                        <span class="font-semibold text-white">{{ $comment->user->full_name }}</span>
                        <span class="text-xs text-gray-300 ml-2 px-2 py-0.5 rounded-full {{ $comment->user->role === 'admin' ? 'bg-red-900' : ($comment->user->role === 'trainer' ? 'bg-blue-900' : 'bg-green-900') }}">
                            {{ ucfirst($comment->user->role) }}
                        </span>
                        <span class="text-xs text-gray-500 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @if (auth()->check() && auth()->id() === $comment->user_id)
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-400 hover:text-white">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-[#252525] border border-[#3a3a3a] z-10">
                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" 
                                onsubmit="return confirm('Are you sure you want to delete this comment?');"
                                class="comment-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-500 flex items-center w-full p-3 text-sm">
                                    <i class="fas fa-trash-alt mr-2"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            <p class="mt-1 text-gray-200">{{ $comment->content }}</p>
        </div>
    </div>
</div> 