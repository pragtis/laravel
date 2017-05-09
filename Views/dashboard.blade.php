@extends('admin')

@section('content')

<div class="span9" id="content">

    @include('FlashMsg/message')

    <div class="row-fluid">
        <div class="block block_dash">

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($intUserCnt) }}</h3>
                        <p class="txtCenter">{{"Total users registered"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>
            
            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($genreCount) }}</h3>
                        <p class="txtCenter">{{"Total Active Genres"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($reqGenreCount) }}</h3>
                        <p class="txtCenter">{{"Total Genres Requested"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($showsCount) }}</h3>
                        <p class="txtCenter">{{"Total Shows"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($newsCount) }}</h3>
                        <p class="txtCenter">{{"Total News"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($videosCount) }}</h3>
                        <p class="txtCenter">{{"Total Videos"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($audiosCount) }}</h3>
                        <p class="txtCenter">{{"Total Albums"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ number_format($transactionCount) }}</h3>
                        <p class="txtCenter">{{"Total Transactions"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6 dashboard_blocks">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 class="txtCenter">{{ '$'. number_format($totalOfPayment,2) }}</h3>
                        <p class="txtCenter">{{"Total amount received"}}</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <div class="small-box-footer"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection