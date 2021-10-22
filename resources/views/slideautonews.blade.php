<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>SLIDER AUTO NOTICIAS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/all.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/bootstrap-fileinput/css/fileinput.min.css?v=4.5.2") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/select2/select2.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.skinNice.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/skins/skin-green.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/laravel-admin/laravel-admin.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/nprogress/nprogress.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/sweetalert2/dist/sweetalert2.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/nestable/nestable.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/toastr/build/toastr.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/bootstrap3-editable/css/bootstrap-editable.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/google-fonts/fonts.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.css">
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.4/jquery.mobile-1.4.4.min.js"></script>
    <link href="{{url('/css/slideautonews.css')}}" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400&display=swap" rel="stylesheet">

    <script src="http://127.0.0.1:8000/js/app.js"></script>
  </head>
  <body class="hold-transition skin-green sidebar-mini sidebar-collapse">
    <div class="wrapper">
      <header class="main-header">
        <a href="{{ admin_url('/') }}" target="_blank" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>CP</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>COPO</b> admin</span>
        </a>
      <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <span style="color:white;display: inline-block;height: 40px;padding-top: 10px;padding-left: 20px;font-size: 25px;">SLIDE AUTO NOTAS</span>
        </nav>
      </header>

    </div>
      <select class="select_states" name="">
        <option value="-1"> SELECCIONA UN ESTADO </option>
        @foreach($states as $state)
          <option value="{{ $state->d_estado }}">{{ $state->d_estado }}</option>
        @endforeach
      </select>
    <div id="container">
      @php
        $incrementNew = 0
      @endphp
      @foreach ($news as $new)
        @if ($incrementNew == 0)
          @php
            $incrementNew = 1
          @endphp
          <div class="buddy" style="display:block">
            <span class="idNoticia" style="display:none">{{ $new->idNoticia }}</span>
            <span class="idNoticiaAuto" style="display:none">{{ $new->idNoticiaAuto }}</span>

            <div class="row">
              <div class="col-3">
                @if (strpos($new->imagen, 'http') !== false)
                  <div class="avatar" style="display: block; background-image: url({{ $new->imagen }})"></div>
                @else
                  <div class="avatar" style="display: block;  background-size: 114px 114px;; background-image: url(/images/logo-gris.png)"></div>
                @endif
              </div>

              <div class="col-9" style="margin-left: -65px!important;">
                <span class="url" style="display:block;color:#4400FF;margin-top: 50px;margin-bottom: 27px;padding-top: 40px;font-weight:bolder;">URL: {{ $new->url }}</span>
                <div style="color:#000000">
                  <span style="display: block">COINCIDENCIAS:</span>
                    @if ($new->estado != "")
                      <span style="display: block">Estado: {!! $new->estado !!}</span>
                    @endif
                    @if ($new->municipio != "")
                      <span style="display: block">Municipio: {{ $new->municipio }}</span>
                    @endif
                    @if ($new->asentamiento != "")
                      <span style="display: block">Asentamiento: {{ $new->asentamiento }}</span>
                    @endif
                    @if ($new->codigoPostal != "")
                      <span style="display: block">Codigo Postal: {{ $new->codigoPostal }}</span>
                    @endif
                    @if ($new->puntuacion != "")
                      <span style="display: block">Puntuación: {{ $new->puntuacion }}</span>
                    @endif
                </div>
              </div>
            </div>

            <div class="row mt-2 ms-4">
              <div class="col">
                <span style="display: block; width: 97%;border-bottom: 2px solid #9D9D9D;margin-bottom:30px;margin-top:60px"></span>
                <span style="color: #000000;display:block; font-family: 'Lato', sans-serif; font-size:24px; font-weight:bold">
                  {!! $new->titulo !!}
                </span>
                <span class="mt-2" style="color: #000000;display:block; font-family: 'Lato', sans-serif; font-size:18px">
                  {{ $new->sumario }}
                </span>
                <span class="mt-2" style="color: #575757!important;display:block; font-family: 'Lato'!important, sans-serif; font-size:18px!important;">
                  {!! strip_tags($new->contenido) !!}
                </span>
              </div>
            </div>

            <div class="row mt-4 me-2 ms-4">
              <div class="col-6">
              </div>
              <div class="col-3">
                  <button type="button" class="cancel-button" style="background-color: #707070;width:85%;color:white;float: right" alt="cancel-button">RECHAZAR</button>
              </div>
              <div class="col-3">
                <button type="button" class="ok-button" style="background-color: #4400FF;width:85%;color:white;float:right;margin-right:20px" alt="ok-button">ACEPTAR</button>
              </div>
            </div>
          </div>
        @else
          <div class="buddy">
            <span class="idNoticia" style="display:none">{{ $new->idNoticia }}</span>
            <span class="idNoticiaAuto" style="display:none">{{ $new->idNoticiaAuto }}</span>
            <span class="url" style="display:none">{{ $new->url }}</span>
            {{-- <div class="left">
              <div class="avatar" style="display: block; background-image: url({{ $new->imagen }})"></div>
            </div> --}}

            <div class="row">
              <div class="col-3">
                @if (strpos($new->imagen, 'http') !== false)
                  <div class="avatar" style="display: block; background-size: 220px 220px; ;background-image: url({{ $new->imagen }})"></div>
                @else
                  <div class="avatar" style="display: block;  background-size: 114px 114px; background-image: url(/images/logo-gris.png)"></div>
                @endif
              </div>
              <div class="col-9" style="margin-left: -65px!important;">
                <span class="url" style="display:block;color:#4400FF;margin-top: 70px;margin-bottom: 30px;padding-top: 40px;font-weight:bolder;">URL: {{ $new->url }}</span>
                <div style="color:#000000">
                  <span style="display: block">COINCIDENCIAS:</span>
                    @if ($new->estado != "")
                      <span style="display: block">Estado: {!! $new->estado !!}</span>
                    @endif
                    @if ($new->municipio != "")
                      <span style="display: block">Municipio: {{ $new->municipio }}</span>
                    @endif
                    @if ($new->asentamiento != "")
                      <span style="display: block">Asentamiento: {{ $new->asentamiento }}</span>
                    @endif
                    @if ($new->codigoPostal != "")
                      <span style="display: block">Codigo Postal: {{ $new->codigoPostal }}</span>
                    @endif
                    @if ($new->puntuacion != "")
                      <span style="display: block">Puntuación: {{ $new->puntuacion }}</span>
                    @endif
                </div>
              </div>
            </div>

            <div class="row mt-2 ms-4">
              <div class="col">
                <span style="display: block; width: 97%;border-bottom: 2px solid #9D9D9D;margin-bottom:30px;margin-top:60px"></span>
                <span style="color: #000000;display:block; font-family: 'Lato', sans-serif; font-size:24px; font-weight:bold">
                  {!! $new->titulo !!}
                </span>
                <span class="mt-2" style="color: #000000;display:block; font-family: 'Lato', sans-serif; font-size:18px">
                  {{ $new->sumario }}
                </span>
                <span class="mt-2" style="color: #575757!important;display:block; font-family: 'Lato'!important, sans-serif; font-size:18px!important;">
                  {!! strip_tags($new->contenido) !!}
                </span>
              </div>
            </div>

            <div class="row mt-4 me-2 ms-4">
              <div class="col-6">
              </div>
              <div class="col-3">
                  <button type="button" class="cancel-button" style="background-color: #707070;width:85%;color:white;float: right" alt="cancel-button">RECHAZAR</button>
              </div>
              <div class="col-3">
                <button type="button" class="ok-button" style="background-color: #4400FF;width:85%;color:white;float:right;margin-right:20px" alt="ok-button">ACEPTAR</button>
              </div>
            </div>

          </div>
        @endif
      @endforeach
    </div>
    <script type="text/javascript">
    $(document).ready(function(){
      $(".select_states").change(function(){
        var nameState = $(this).find("option:selected").attr("value");

        $.ajax({
          url: "/admin/api/findnewstoslide",
          type: "get", //send it through get method
          data: {
            state: nameState
          },
          success: function(response) {
            $("#container").html(response);
          },
          error: function(xhr) {
            //Do Something to handle error
          }
        });
      });
      $(document).on("swiperight",".buddy",function(){
        $(this).addClass('rotate-left').delay(700).fadeOut(1);
        $('.buddy').find('.status').remove();

        $(this).append('<div class="status like">Like!</div>');
        var oThis = this;
        $.getJSON(
          '/admin/api/swiperightnews',
          {
            idAuto: $(oThis).find(".idNoticiaAuto").text(),
            idNews:$(oThis).find(".idNoticia").text(),
            url: $(oThis).find(".url").text()
          },
          function(data,textStatus,jqXHR){
            console.log(data);
          }
        );
        $(this).next().removeClass('rotate-left rotate-right').fadeIn(400).promise().done(function(){
          $(oThis).remove();
          if($(".buddy").length == 0){
            $("#container").append("<div class='buddy' style='display:block;'><span class='title'>Ya no hay mas noticias que revisar, muy bien hecho (Y).</span></div>");
          }
        });
      });

      $(document).on("swipeleft",".buddy",function(){
        $(this).addClass('rotate-right').delay(700).fadeOut(1);
        $('.buddy').find('.status').remove();

        $(this).append('<div class="status dislike">Dislike!</div>');
        var oThis = this;
        $.getJSON(
          '/admin/api/swipeleftnews',
          {
            idAuto: $(oThis).find(".idNoticiaAuto").text(),
            idNews:$(oThis).find(".idNoticia").text(),
            url: $(oThis).find(".url").text()
          },
          function(data,textStatus,jqXHR){
            console.log(data);
          }
        );
        $(this).next().removeClass('rotate-left rotate-right').fadeIn(400).promise().done(function(){
          $(oThis).remove();
          if($(".buddy").length == 0){
            $("#container").append("<div class='buddy' style='display:block;'><span class='title'>Ya no hay mas noticias que revisar, muy bien hecho (Y).</span></div>");
          }
        });
      });

      $(document).on("click",".cancel-button",function(){
        $(this).closest(".buddy").trigger("swipeleft");
        console.log("cancelar");
      });
      $(document).on("click",".ok-button",function(){
        $(this).closest(".buddy").trigger("swiperight");
        console.log("aceptar");
      });
    });
    </script>
  </body>
</html>
