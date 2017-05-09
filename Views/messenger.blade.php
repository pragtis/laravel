@extends('Layouts.home')
@section('content')
<section class="main-content">
    <div class="wrap__">
        <main>
            <div class="container__" id="siteMessenger">
                <!-- Inbox Message List Start -->
                <div class="ibx-msg-wrp">
                    <div class="msg-left header_shown">
                        <ul class="inbox-msg-list content" id="msgConversationUsers">
                            @include('Messages.messenger_conversation')
                        </ul>
                    </div>
                    <div class="msg-content-wizard header_shown">
                        <div class="msg-content">
                            <div class="ibx-header">
                                <div class="pull-right">
                                    <i class="glyphicon glyphicon-th show_header_pallet curpoint"></i>
                                </div>
                                <div class="pull-right new_msg_btn disp_none" id="conversation_loader">
                                    <img src="/img/ajax-loader.gif" alt="Loading..." title="Loading..." />
                                </div>
                                <div class="ellipses mesg-chat_head">
                                    <span id="conversation_name" class="{{ !empty($arrSelectedProfile) ? '' : 'disp_none' }}">
                                        <strong id="conversation_name_slug">
                                            {{ !empty($arrSelectedProfile->name) ? $arrSelectedProfile->name : '' }}
                                        </strong>
                                    </span>
                                </div>
                            </div>
                            <div class="ibx-usr-msgs" id="messagesSection">
                                @include('Messages.messenger_list')
                            </div>
                            <div class="ibx-reply">
                                <div class="pos_rela">
                                    <div class="msg-overlay disp_none" id="messenger_overlay"></div>
                                    {!! Form::open(array('class' => 'form', 'id' => 'sendUserMsg', 'enctype' => 'multipart/form-data')) !!}
                                    {!! Form::hidden('messageTo', $selectedUserId, array('id' => 'sendMessageTo')) !!}
                                    {!! Form::textarea('message', null, array('class'=>'', 'id' => 'user_message', 'placeholder'=>'Type your message...')) !!}
                                    {!! Form::button('', array('type'=>'submit', 'id' => 'msgFrmSubBtn', 'class'=>'btn btn-cus-msg')) !!}
                                    {!! Form::close() !!}
                                    <div class="pres_ent_wizard">
                                        <div class="remb-label2">
                                            <label id="sendOnEnterHit" class="css-label lite-green-check send_pres_ent_wizard" for="toggle_enterclick">Send on enter key hit</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Inbox Message List End -->
              
            </div>
        </main>
    </div>
</section>
<!-- MAIN CONTENT END -->
@endsection