<?php

namespace App\Http\Controllers\PetLover;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adoption;
use App\Models\Pet;
use App\Models\MedicalInfo;
use App\Models\Message;
use App\Models\User;
use App\Models\AdoptionRequest;
use App\Models\Notification;

use Illuminate\Support\Facades\Auth;

class PetLoverController extends Controller
{
    public function dashboard() {
        $user = Auth::user();

        // Only pets NOT owned by me
        $petAvailable = Pet::where('status', 'available')
                           ->where('owner_id', '!=', Auth::id())
                           ->count();

        // MY pending adoption requests (as adopter)
        $request = AdoptionRequest::where('adopter_id', Auth::id())
                                  ->where('status', 'pending')
                                  ->count();

        // Unread messages sent TO me
        $messages = Message::where('receiver_id', Auth::id())
                           ->where('is_read', 0)
                           ->count();

        // Pets I posted that got rehomed
        $rehomed = Pet::where('owner_id', Auth::id())
                      ->where('status', 'rehomed')
                      ->count();

        $pets = Pet::where('status', 'available')
            ->where('owner_id', '!=', Auth::id())
            ->whereHas('owner', function ($q) use ($user) {
                $q->where('address', $user->address);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4, ['*'], 'dash_page');

        return view('pet-lover.dashboard', compact('petAvailable', 'request', 'messages', 'rehomed', 'pets'));
    }

    public function pet_marketplace(Request $request) {
        $query = Pet::where('status', 'available')
                    ->where('owner_id', '!=', Auth::id())
                    // ✅ HARD GUARD: exclude pets that already have an adoption record
                    ->whereDoesntHave('adoption');

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest_arrival': $query->orderBy('created_at', 'desc'); break;
                case 'longest_stay':   $query->orderBy('created_at', 'asc');  break;
                case 'age_desc':       $query->orderBy('birthday', 'desc');   break;
                case 'age_asc':        $query->orderBy('birthday', 'asc');    break;
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $pets = $query->with(['owner', 'medicalRecord'])->paginate(12);

        return view('pet-lover.pet-marketplace', compact('pets'));
    }

    public function rehoming_center() {
        // ── Stats (count all my pets, not just the current page) ──
        $petsAll = Pet::where('owner_id', Auth::id())
                    ->with(['owner', 'medicalRecord', 'adoptionRequests'])
                    ->get();

        $petPosted       = $petsAll->count();
        $rehomedPets     = $petsAll->where('status', 'rehomed')->count();
        $pendingRequests = AdoptionRequest::whereHas('pet', function($q) {
                                $q->where('owner_id', Auth::id());
                            })->where('status', 'pending')->count();

        // ── Paginated table (10 per page) ──
        $pets = Pet::where('owner_id', Auth::id())
                    ->with(['owner', 'medicalRecord', 'adoptionRequests'])
                    ->paginate(10, ['*'], 'rehome_page');

        return view('pet-lover.rehoming-center', compact('pets', 'rehomedPets', 'pendingRequests', 'petPosted'));
    }

    public function post_pet(Request $request) {
        $request->validate([
            'name'        => 'required|string|max:50',
            'breed'       => 'required|string|max:50',
            'gender'      => 'required',
            'birthday'    => 'required|date',
            'likes'       => 'required|string',
            'dislikes'    => 'required|string',
            'personality' => 'required|string',
            'pet_image'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'vaccinated'              => 'nullable|boolean',
            'vaccinated_date'         => 'nullable|date',
            'vaccinated_certificate'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'dewormed'                => 'nullable|boolean',
            'dewormed_date'           => 'nullable|date',
            'dewormed_certificate'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'neutered'                => 'nullable|boolean',
            'neutered_date'           => 'nullable|date',
            'neutered_certificate'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $imagePath = 'assets/cutiepic.png';
        if ($request->hasFile('pet_image')) {
            $imagePath = $request->file('pet_image')->store('pets', 'public');
        }

        $pet = Pet::create([
            'owner_id'    => Auth::id(),
            'name'        => $request->name,
            'breed'       => $request->breed,
            'gender'      => $request->gender,
            'birthday'    => $request->birthday,
            'likes'       => $request->likes,
            'dislikes'    => $request->dislikes,
            'personality' => $request->personality,
            'pet_image'   => $imagePath,
            'status'      => 'available',
        ]);

        $vaccinatedCert = null;
        if ($request->hasFile('vaccinated_certificate')) {
            $vaccinatedCert = $request->file('vaccinated_certificate')->store('medical', 'public');
        }
        $dewormedCert = null;
        if ($request->hasFile('dewormed_certificate')) {
            $dewormedCert = $request->file('dewormed_certificate')->store('medical', 'public');
        }
        $neuteredCert = null;
        if ($request->hasFile('neutered_certificate')) {
            $neuteredCert = $request->file('neutered_certificate')->store('medical', 'public');
        }

        MedicalInfo::create([
            'pet_id'                 => $pet->pet_id,
            'vaccinated'             => $request->boolean('vaccinated'),
            'vaccinated_date'        => $request->vaccinated_date ?? null,
            'vaccinated_certificate' => $vaccinatedCert,
            'dewormed'               => $request->boolean('dewormed'),
            'dewormed_date'          => $request->dewormed_date ?? null,
            'dewormed_certificate'   => $dewormedCert,
            'neutered'               => $request->boolean('neutered'),
            'neutered_date'          => $request->neutered_date ?? null,
            'neutered_certificate'   => $neuteredCert,
        ]);

        return redirect()->route('petlover.rehoming-center')->with('success', 'Pet successfully listed!');
    }

    public function adoption_tracker() {
        $userId = Auth::id();

        $sentRequests = AdoptionRequest::where('adopter_id', $userId)
            ->with(['pet.owner', 'adoption'])
            ->get();

        $receivedRequests = AdoptionRequest::whereHas('pet', function($q) use ($userId) {
                $q->where('owner_id', $userId);
            })
            ->with(['pet.owner', 'adopter', 'adoption'])
            ->get();

        return view('pet-lover.adoption-tracker', compact('sentRequests', 'receivedRequests'));
    }

    public function adoption_transcript($id)
    {
        $adoption = Adoption::with(['pet', 'adopter', 'giver', 'admin'])
            ->findOrFail($id);

        // Security check (VERY important, don’t remove)
        if (
            $adoption->adopter_id !== Auth::id() &&
            $adoption->giver_id !== Auth::id()
        ) {
            abort(403);
        }

        return view('pet-lover.adoption-transcript', compact('adoption'));
    }

    public function adoption_request(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,pet_id'
        ]);

        $pet = Pet::findOrFail($request->pet_id);

        if ($pet->owner_id == Auth::id()) {
            return redirect()->back()->with('error', 'You cannot adopt your own pet!');
        }

        $exists = AdoptionRequest::where('pet_id', $request->pet_id)
                                 ->where('adopter_id', Auth::id())
                                 ->exists();

        if ($exists) {
            return redirect()->back()->with('info', 'You have already sent a request for this pet.');
        }

        AdoptionRequest::create([
            'pet_id'     => $request->pet_id,
            'adopter_id' => Auth::id(),
            'status'     => 'pending'
        ]);

        // Notify the pet owner
        Notification::create([
            'user_id'      => $pet->owner_id,
            'type'         => 'adoption_request',
            'title'        => 'New Adoption Request',
            'message'      => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' wants to adopt your pet ' . $pet->name . '!',
            'related_id'   => $pet->pet_id,
            'related_type' => 'pet',
        ]);

        return redirect()->route('petlover.adoption-tracker')->with('success', 'Adoption request sent!');
    }

    public function adoption_accept($id)
    {
        $adoptionRequest = AdoptionRequest::with('pet')->findOrFail($id);

        if ($adoptionRequest->pet->owner_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $adoptionRequest->update(['status' => 'accepted']);
        $adoptionRequest->pet->update(['status' => 'rehomed']);

        // ✅ FIX: include ALL required fields based on your migration
        Adoption::create([
            'pet_id'        => $adoptionRequest->pet_id,
            'adopter_id'    => $adoptionRequest->adopter_id,
            'giver_id'      => $adoptionRequest->pet->owner_id, // 👈 owner = giver
            'request_id'    => $adoptionRequest->request_id,    // 👈 required
            'approved_by'   => Auth::id(),                      // 👈 optional but correct
            'adoption_date' => now(),
        ]);

        // Reject all other pending requests for the same pet
        AdoptionRequest::where('pet_id', $adoptionRequest->pet_id)
            ->where('request_id', '!=', $id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Notify the adopter
        Notification::create([
            'user_id'      => $adoptionRequest->adopter_id,
            'type'         => 'adoption_accepted',
            'title'        => 'Adoption Request Accepted',
            'message'      => 'Your request to adopt ' . $adoptionRequest->pet->name . ' has been accepted!',
            'related_id'   => $adoptionRequest->request_id,
            'related_type' => 'adoption_request',
        ]);

        return redirect()->route('petlover.adoption-tracker')->with('success', 'Adoption accepted!');
    }

    public function adoption_decline($id)
    {
        $adoptionRequest = AdoptionRequest::with('pet')->findOrFail($id);

        if ($adoptionRequest->pet->owner_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $adoptionRequest->update(['status' => 'rejected']);

        // Notify the adopter
        Notification::create([
            'user_id'      => $adoptionRequest->adopter_id,
            'type'         => 'adoption_declined',
            'title'        => 'Adoption Request Declined',
            'message'      => 'Your request to adopt ' . $adoptionRequest->pet->name . ' has been declined.',
            'related_id'   => $adoptionRequest->request_id,
            'related_type' => 'adoption_request',
        ]);

        return redirect()->route('petlover.adoption-tracker')
            ->with('success', 'Request declined.');
    }

    public function adoption_cancel($id)
    {
        $adoptionRequest = AdoptionRequest::findOrFail($id);

        if ($adoptionRequest->adopter_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($adoptionRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request can no longer be cancelled.');
        }

        $adoptionRequest->delete();

        return redirect()->route('petlover.adoption-tracker')
            ->with('success', 'Adoption request cancelled.');
    }

    public function community_inbox(Request $request)
    {
        $userId = Auth::id();

        // ── Build full conversation partner list ──────────────────
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($msg) use ($userId) {
                return $msg->sender_id == $userId ? $msg->receiver : $msg->sender;
            })
            ->filter(function ($user) use ($userId) {
                return $user && $user->user_id != $userId;
            })
            ->unique('user_id')
            ->values();

        // ── Auto-prepend ?with= user if not already present ───────
        $openUserId = $request->query('with');
        if ($openUserId && (int) $openUserId !== (int) $userId) {
            $alreadyIn = $conversations->contains('user_id', (int) $openUserId);
            if (!$alreadyIn) {
                $targetUser = User::find($openUserId);
                if ($targetUser) {
                    $conversations->prepend($targetUser);
                }
            }
        } else {
            $openUserId = null;
        }

        // ── Stats ─────────────────────────────────────────────────
        $unreadCount  = Message::where('receiver_id', $userId)->where('is_read', 0)->count();
        $inquiryCount = Message::where('receiver_id', $userId)->count();

        // ── Split into buckets ────────────────────────────────────
        $archivedUserIds = session()->get('archived_users', []);
        $blockedUserIds  = session()->get('blocked_users',  []);

        $archivedConversations = $conversations->filter(fn($u) =>  in_array($u->user_id, $archivedUserIds))->values();
        $blockedConversations  = $conversations->filter(fn($u) =>  in_array($u->user_id, $blockedUserIds))->values();
        $conversations         = $conversations->filter(fn($u) => !in_array($u->user_id, $archivedUserIds)
                                                                 && !in_array($u->user_id, $blockedUserIds))->values();

        // ── Total count across all tabs (for the Inquiries stat card) ──
        $totalConversations = $conversations->count()
                            + $archivedConversations->count()
                            + $blockedConversations->count();

        return view('pet-lover.community-inbox', compact(
            'conversations',
            'archivedConversations',
            'blockedConversations',
            'openUserId',
            'unreadCount',
            'inquiryCount',
            'totalConversations'    // ← NEW: passed to fix the Inquiries stat card
        ));
    }

    public function community_inbox_messages($userId)
    {
        $authId = Auth::id();
        $userId = (int) $userId;

        $messages = Message::where(function($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function community_inbox_send(Request $request)
    {
        $request->validate([
            'receiver_id'  => 'required|exists:users,user_id',
            'message_text' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id'    => Auth::id(),
            'receiver_id'  => $request->receiver_id,
            'message_text' => $request->message_text,
            'is_read'      => 0,
        ]);

        Notification::create([
            'user_id'      => $request->receiver_id,
            'type'         => 'new_message',
            'title'        => 'New Message',
            'message'      => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' sent you a message.',
            'related_id'   => $message->message_id,
            'related_type' => 'message',
        ]);

        return response()->json($message);
    }

    public function community_inbox_send_image(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('messages', 'public');

        $message = Message::create([
            'sender_id'    => Auth::id(),
            'receiver_id'  => $request->receiver_id,
            'message_text' => 'image::' . $imagePath,
            'is_read'      => 0,
        ]);

        Notification::create([
            'user_id'      => $request->receiver_id,
            'type'         => 'new_message',
            'title'        => 'New Message',
            'message'      => Auth::user()->first_name . ' ' . Auth::user()->last_name . ' sent you an image.',
            'related_id'   => $message->message_id,
            'related_type' => 'message',
        ]);

        return response()->json($message);
    }

    public function community_inbox_mark_read($userId)
    {
        $userId = (int) $userId;
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function community_inbox_archive(Request $request)
    {
        $userId   = (int) $request->input('user_id');
        $archived = session()->get('archived_users', []);

        if (!in_array($userId, $archived)) {
            $archived[] = $userId;
            session()->put('archived_users', $archived);
        }

        return response()->json(['success' => true]);
    }

    public function community_inbox_block(Request $request)
    {
        $userId  = (int) $request->input('user_id');
        $blocked = session()->get('blocked_users', []);

        if (!in_array($userId, $blocked)) {
            $blocked[] = $userId;
            session()->put('blocked_users', $blocked);
        }

        return response()->json(['success' => true]);
    }

    public function aboutMe()
    {
        $user   = Auth::user();
        $myPets = $user->pets;
        return view('shared.about-me', compact('user', 'myPets'));
    }

    public function notifications_mark_read()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}