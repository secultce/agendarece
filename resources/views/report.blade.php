<html>
    <head>
        <meta charset="UTF-8">
        <title>Relatório de Programações | {{ $period }}</title>
    </head>
    <style>
        @font-face {
            font-family: 'Nunito';
            src: url({{ storage_path('fonts/Nunito-Regular.ttf') }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Nunito';
            src: url({{ storage_path('fonts/Nunito-Regular.ttf') }}) format("truetype");
            font-weight: bold;
            font-style: normal;
        }

        * {
            font-family: 'Nunito' !important;
        }

        table.bordered {
            width: 100%;
            border: 1px solid #e3e3e3;
        }

        .no-border-bottom {
            border-bottom: none !important;
        }

        .no-border-top {
            border-top: none !important;
        }

        .bg-grey {
            background-color: #f9fafc;
        }

        th {
            color: rgba(0, 0, 0, 0.6);
            padding: 5px 5px 10px;
        }

        td {
            padding: 5px 5px 10px;
        }

        body {
            background-color: transparent;
        }

        .page-break {
            page-break-after: always;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .font-size-1 {
            font-size: 1.1rem;
        }

        .text-start {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        table, tr, td, th, tbody, thead, tfoot {
            page-break-inside: avoid !important;
        }

        .text-justify {
            text-align: justify;
        }

        table tbody td {
            font-size: 12px;
        }

        table thead th {
            font-size: 14px;
        }

        .color-preview {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 3px;
            border: 0.1px solid #e4e4e4;
            max-width: 20px;
        }
    </style>

    <body>
        <header>
            <h1 class="mb-0 mt-0">Relatório de Programações</h1>
            <p class="mt-0 mb-0 font-size-1">Agenda: {{ $schedule }}</p>
            <p class="mt-0 font-size-1">Período: {{ $period }}</p>
        </header>
        <hr>
        <main>
            @php
                $programmationGroups = $programmations->groupBy(fn ($programmation) => date('Y-m', strtotime($programmation->start_date)));
            @endphp

            @foreach ($programmationGroups as $period => $programmationGroup)
                <h3>{{ ucfirst(\Carbon\Carbon::parse($period)->formatLocalized('%B de %Y')) }}</h3>

                <table class="bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="text-start">Data da Programação</th>
                            <th class="text-start">Título</th>
                            <th class="text-center">Categoria</th>
                            <th class="text-start">Espaços</th>
                            <th class="text-start">Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programmationGroup as $programmation)
                            <tr>
                                <td class="text-center"><div class="color-preview" style="background-color: {{ $programmation->category->color }}"></div></td>
                                <td class="text-start">
                                    @if ($programmation->end_date)
                                        @if ($programmation->start_date === $programmation->end_date)
                                            {{ date('d/m', strtotime($programmation->start_date)) }} das {{ substr($programmation->start_time, 0, -3) }} até {{ substr($programmation->end_time, 0, -3) }}
                                        @else
                                            {{ date('d/m', strtotime($programmation->start_date)) }} à {{ date('d/m', strtotime($programmation->end_date)) }} das {{ substr($programmation->start_time, 0, -3) }} até {{ substr($programmation->end_time, 0, -3) }}
                                        @endif
                                    @else
                                        {{ date('d/m', strtotime($programmation->start_date)) }} das {{ substr($programmation->start_time, 0, -3) }} até {{ substr($programmation->end_time, 0, -3) }} (sem data de término)
                                    @endif
                                </td>
                                <td class="text-start">{{ $programmation->title }}</td>
                                <td class="text-center">{{ $programmation->category->name }}</td>
                                <td class="text-start">{{ $programmation->spaces->implode('space.name', ' | ') }}</td>
                                <td class="text-start">{{ $programmation->description ?? "Sem descrição" }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </main>
    </body>
</html>