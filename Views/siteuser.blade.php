@extends('admin')

@section('content')
    
    <div class="span9" id="content">

        @include('FlashMsg/message')
        
	<div class="row-fluid">
	   <!-- block -->
	   <div class="block clearfix">
	       <div class="navbar navbar-inner block-header">
		   <div class="muted pull-left">Add Site User</div>
	       </div>
	       <div class="block-content collapse in">
                    <div class="span12">
		    
                        {!! Form::open(array('class' => 'form')) !!}
                        
                        {!! Form::label('Name*') !!}
                        {!! Form::text('name', null, 
                            array('class'=>'input-block-level', 'id' => 'name', 'placeholder'=>'Name')) !!}
                        
                        {!! Form::label('E-Mail Address*') !!}
                        {!! Form::text('email_id', null, 
                            array('class'=>'input-block-level', 'id' => 'email_id', 'placeholder'=>'E-Mail Address')) !!}
                            
                        {!! Form::label('Password*') !!}
                        {!! Form::password('password',  
                            array('class'=>'input-block-level', 'id' => 'password', 'placeholder'=>'Password')) !!}
                            
                        {!! Form::label('Confirm Password*') !!}
                        {!! Form::password('password_confirmation', 
                            array('class'=>'input-block-level', 'id' => 'password_confirmation', 'placeholder'=>'Confirm Password')) !!}
                            
                        {!! Form::submit('Save', array('class'=>'btn btn-primary')) !!}
                        
                        {!! Form::close() !!}
		    
                    </div>
	       </div>
	   </div>
	   <!-- /block -->
       </div>
    
    </div>
@endsection