<table>
    <tr>
        <td>#</td>
        <td style="width:30px;">First Name</td>
        <td style="width:30px;">Last Name</td>
        <td style="width:30px;">Email</td>
        <td style="width:30px;">Registerd Date</td>
        <td style="width:30px;">Gender</td>
        <td style="width:30px;">State</td>
        <td style="width:30px;">City</td>
        <td style="width:30px;">Address</td>
        <td style="width:30px;">Postal</td>
        <td style="width:30px;">Date Of Birth</td>
        <td style="width:30px;">Merchant Id</td>
        <td style="width:30px;">Merchant Security</td>
    </tr>
    @foreach ($users as $user)
    <tr>
        <td>
            {{$user['id']}}
        </td>
        <td>
            {{$user['first_name']}}
        </td>
        <td>
            {{$user['last_name']}}
        </td>
        <td>
            {{$user['email']}}
        </td>
        <td>
            {{$user['created_at']}}
        </td>
        <td>
            {{$user['gender']}}
        </td>
        <td>
            {{$user['state']}}
        </td>
        <td>
            {{$user['city']}}
        </td>
        <td>
            {{$user['address']}}
        </td>
        <td>
            {{$user['postal']}}
        </td>
        <td>
            {{$user['dob']}}
        </td>
        <td>
            {{$user['merchant_id']}}
        </td>
        <td>
            {{$user['merchant_security']}}
        </td>
    </tr>
    @endforeach
</table>