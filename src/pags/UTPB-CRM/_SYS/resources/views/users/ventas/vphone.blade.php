@extends('users.'.Auth::user()->level->alias.'.home')
@section('content')
  <table>
    <tr>
      <td>

      </td>
      <td>
        <iframe src="https://unisant.awsapps.com/connect/ccp-v2/softphone" width="100%" height="height:100%"></iframe>
      </td>
    </tr>
  </table>

@endsection
