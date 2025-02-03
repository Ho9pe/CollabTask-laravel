<x-mail::message>
# You've Been Invited to Join a Task Group

{{ $invitation->inviter->name }} has invited you to join the task group "{{ $group->name }}".

<x-mail::panel>
{{ $group->description }}
</x-mail::panel>

<x-mail::button :url="route('groups.invitations.accept', $invitation)" color="success">
Accept Invitation
</x-mail::button>

<x-mail::button :url="route('groups.invitations.reject', $invitation)" color="error">
Decline
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> 