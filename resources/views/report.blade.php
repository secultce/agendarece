<html>
    <head>
        <meta charset="UTF-8">
        <title>Programação | {{ $period }}</title>
    </head>
    <style>
        @font-face {
            font-family: 'Arial';
            src: url({{ storage_path('fonts/default/arial.ttf') }}) format("truetype");
        }

        @font-face {
            font-family: 'Arial';
            src: url({{ storage_path('fonts/default/arial-bold.ttf') }}) format("truetype");
            font-weight: bold;
        }
        
        * {
            font-family: 'Arial' !important;
        }

        .m-0 {
            margin: 0;
        }

        .logo {
            margin-bottom: 20px;
        }

        .primary-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .secondary-title {
            font-size: 15px;
            text-transform: uppercase;
            margin: 0;
        }

        hr {
            height: 1px;
            border: none;
            background: #000;
            margin: 0;
        }

        header {
            margin-bottom: 20px;
            text-align: center;
        }

        .group-header {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: right;
        }

        .programmation-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0 0;
        }

        .programmation-group {
            margin: 30px 0;
        }

        .programmation-description {
            margin: 0 0 20px; 
        }

        .page-break {
            page-break-after: always;
        }
    </style>

    <body>
        <header>
            @if ($logo) <img class="logo" src="data:image/jpg;base64,{{ $logo }}" width="250px" style="margin: 0 auto;"> @endif
            <h1 class="primary-title">RELATÓRIO DE PROGRAMAÇÃO - {{  $period }}</h1>
            <p class="secondary-title">(Agenda {{ $schedule }})</p>
        </header>

        <main>
            @php
                $programmationGroups = $programmations->groupBy(fn ($programmation) => date('Y-m', strtotime($programmation->start_date)));
                $programmationGroupIndex = 0;
            @endphp

            @foreach ($programmationGroups as $period => $programmationGroup)
                <div>
                    <h2 class="group-header">{{ ucfirst(\Carbon\Carbon::parse($period)->formatLocalized('%B de %Y')) }}</h2>
                </div>
                <hr>

                @foreach ($programmationGroup as $programmation)
                    <h3 class="programmation-title">{{ $programmation->title }}</h3>

                    <div class="programmation-group">
                        <p class="m-0">
                            Período:

                            @if ($programmation->end_date)
                                @if ($programmation->start_date === $programmation->end_date)
                                    {{ ucfirst(\Carbon\Carbon::parse($programmation->start_date)->formatLocalized('%d de %B ')) }}
                                @else
                                    {{ ucfirst(\Carbon\Carbon::parse($programmation->start_date)->formatLocalized('%d de %B ')) }} à {{ ucfirst(\Carbon\Carbon::parse($programmation->end_date)->formatLocalized('%d de %B ')) }}
                                @endif
                            @else
                                {{ date('d/m', strtotime($programmation->start_date)) }} (sem data de término)
                            @endif
                        </p>

                        <p class="m-0">
                            Horário: {{ substr($programmation->start_time, 0, -3) }} até {{ substr($programmation->end_time, 0, -3) }}
                        </p>

                        <p class="m-0">
                            Classificação Indicativa: {{ $programmation->parental_rating_alias }}
                        </p>

                        <p class="m-0">
                            Eixo Estratégico: {{ $programmation->category->axis ? $programmation->category->axis->name : 'Nenhum' }}
                        </p>

                        <p class="m-0">
                            Categoria: {{ $programmation->category->name }}
                        </p>

                        <p class="m-0">
                            Política de Ocupação: {{ $programmation->occupation ? $programmation->occupation->name : 'Nenhuma' }}
                        </p>

                        <p class="m-0">
                            Espaço(s):

                            @foreach ($programmation->spaces as $index => $space)
                                {{ $space->space->name }}@if ($index + 1 < $programmation->spaces->count()), @endif
                            @endforeach
                        </p>

                        @if ($programmation->requested_at)
                            <p class="m-0">
                                Solicitação aprovada em: {{ date('d/m/Y', strtotime($programmation->start_date)) }}
                            </p>

                            <p class="m-0">
                                Responsável pela aprovação: {{ $programmation->user->name }}
                            </p>
                        @endif
                    </div>

                    <p class="programmation-description">{{ $programmation->description ?? "Sem descrição" }}</p>
                    <hr>
                @endforeach

                @if (++$programmationGroupIndex < $programmationGroup->count()) 
                    <div class="page-break"></div>
                @endif
            @endforeach
        </main>
    </body>
</html>