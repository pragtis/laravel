@extends('admin')

@section('content')
    
    <div class="span9" id="content">

        @include('FlashMsg/message')
    
	<div class="row-fluid">
	   <!-- block -->
	   <div class="block clearfix">
	       <div class="navbar navbar-inner block-header">
		   <div class="muted pull-left">User Info</div>
	       </div>
	       <div class="block-content collapse in">
		   <div class="span12">
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
			    @if ($user->user_type_id == 1)
                            <tr>
				<th width="20%">Profile Pic</th>
                                <td>
                                    @if (!empty($user->profile_pic) && file_exists(Config::get('constants.USER_PROFILE_PATH').$user->profile_pic))
					<img src="{{ Config::get('constants.VIEW_USER_PROFILE_PATH').$user->profile_pic }}" />
				    @else
					<i class="icon-user"></i>
				    @endif
                                </td>
                            </tr>
			    @endif
                            <tr>
				<th width="20%">Name</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
				<th>Email Address</th>
                                <td>{{ $user->email_id }}</td>
                            </tr>
			    @if ($user->user_type_id == 1)
                            <tr>
				<th>Registration By</th>
                                <td>{{ $arrRegisterBy[$user->register_by] }}</td>
                            </tr>
			    @endif
                            <tr>
				<th>Registration Date</th>
                                <td>{{ date('M d Y h:i A', strtotime($user->created_at)) }}</td>
                            </tr>
                            <tr>
				<th>Status</th>
                                <td>{{ $status }}</td>
			    </tr>
			    @if ($user->user_type_id == 3 || $user->user_type_id == 2)
			    <tr>
				<th>Role</th>
                                <td>{{ $arrRole[$user->user_type_id] }}</td>
			    </tr>
			    <tr>
				<th>Page Permissions</th>
                                <td>{{ ($user->user_type_id == 2) ? 'All Pages' : $strPagePermission }}</td>
			    </tr>
			    @endif
                            @if ($user->user_type_id == 1)
                            <tr>
				<th>General Details</th>
                                <td>{!! nl2br($user->general_details) !!}</td>
			    </tr>
                            <tr>
				<th>Additional Info</th>
                                <td>{!! nl2br($user->additional_info) !!}</td>
			    </tr>
                            <tr>
				<th>Interest</th>
                                <td>{!! nl2br($user->interest) !!}</td>
			    </tr>
                            <tr>
				<th>Facebook Link</th>
                                <td>{{ $user->facebook_profile }}</td>
			    </tr>
                            <tr>
				<th>Twitter Link</th>
                                <td>{{ $user->twitter_profile }}</td>
			    </tr>
                            <tr>
				<th>Genre</th>
                                <td>{{ $strGenres }}</td>
			    </tr>
                            <tr>
				<th>Location</th>
                                <td>{{ $strLocation }}</td>
			    </tr>
                            @endif
			</table>
		    
		   </div>
	       </div>
	   </div>
	   <!-- /block -->
       </div>
    
    </div>

@endsection