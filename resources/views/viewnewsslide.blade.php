<link href="{{url('/css/slideautonews.css')}}" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400&display=swap" rel="stylesheet">

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