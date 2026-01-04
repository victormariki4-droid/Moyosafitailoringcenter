<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fomu ya Kujiunga - {{ $student->first_name }} {{ $student->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Serif:opsz,wght@8..144,400;600;700&display=swap');

        body {
            font-family: 'Roboto Serif', serif;
            background: #e2e8f0;
        }

        .page {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 10mm auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        @media print {
            body {
                background: white;
            }

            .page {
                width: 100%;
                margin: 0;
                padding: 15mm 20mm;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
        }

        .dotted-line {
            border-bottom: 2px dotted #94a3b8;
            flex-grow: 1;
            margin-left: 0.5rem;
            min-width: 50px;
            display: inline-block;
        }
    </style>
</head>

<body>

    <div class="fixed top-4 right-4 no-print z-50">
        <button onclick="window.print()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print Form
        </button>
    </div>

    <div class="page text-gray-900 leading-relaxed">

        <!-- HEADER -->
        <div class="text-center mb-8 relative">
            <div class="flex justify-center items-center gap-4 mb-2">
                <!-- Placeholders for images based on screenshot -->
                <img src="/images/logo.png" class="h-36 object-contain">
                <div>
                    <h1 class="text-2xl font-bold text-blue-900 uppercase">JIMBO KUU KATOLIKI ARUSHA</h1>
                    <h2 class="text-xl font-bold text-red-800 uppercase my-1">PAROKIA YA MOYO SAFI WA BIKIRA MARIA</h2>
                    <h3 class="font-bold text-gray-800">S. L. P 11352 UNGA LIMITED</h3>
                </div>
                {{-- <img src="/images/mary_right.png" class="h-20 object-contain"> --}}
            </div>

            <p class="text-sm text-gray-600">
                Contact: +255 4305 Mobile: +255 672 080 191; +255 658 874 977<br>
                E-mail: moyosafiungaltd@gmail.com
            </p>

            <div class="border-b-4 border-gray-800 border-double mt-2"></div>
        </div>

        <!-- FORM TITLE -->
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-green-800 uppercase">FOMU YA KUJIUNGA NA UFUNDI WA USHONAJI</h2>
            <h3 class="text-lg font-bold text-gray-600 uppercase">NA FANI STADI NYINGINE ZA MAISHA</h3>
        </div>

        <!-- FORM NUMBER & PHOTO -->
        <div class="flex justify-between items-start mb-8 relative">
            <div class="text-xl font-bold text-green-700 mt-4">
                Namba ya Fomu: <span class="text-red-600 ml-2 font-mono">{{ $student->form_number }}</span>
            </div>

            <div
                class="w-32 h-40 border-2 border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden shadow-sm">
                @if($student->profile_photo_path)
                    <img src="{{ asset('storage/' . $student->profile_photo_path) }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xs text-gray-400 text-center px-2">Bandika Picha Hapa</span>
                @endif
            </div>
        </div>

        <!-- SECTION 1 -->
        <div class="mb-8">
            <h4 class="text-lg font-bold text-green-800 uppercase mb-4">1. TAARIFA BINAFSI KWA UJUMLA KWA KILA UFUNDI
            </h4>

            <div class="space-y-3 text-lg">
                <div class="flex items-baseline">
                    <span class="font-bold w-48 shrink-0">• Jina Kamili:</span>
                    <div
                        class="border-b-2 border-dotted border-gray-400 flex-grow font-mono text-blue-900 px-2 uppercase">
                        {{ $student->first_name }} {{ $student->last_name }}
                    </div>
                </div>

                <div class="flex items-baseline">
                    <span class="font-bold w-48 shrink-0">• Tarehe ya Kuzaliwa:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow font-mono text-blue-900 px-2">
                        {{ $student->date_of_birth?->format('d-m-Y') }}
                    </div>
                </div>

                <div class="flex items-baseline">
                    <span class="font-bold w-48 shrink-0">• Mahali unapoishi:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow px-2"></div>
                </div>

                <div class="flex items-baseline">
                    <span class="font-bold w-48 shrink-0">• Namba ya Simu:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow font-mono text-blue-900 px-2">
                        {{ $student->student_phone }}
                    </div>
                </div>

                <div class="flex items-baseline">
                    <span class="font-bold w-48 shrink-0">• Jina la Jumuiya/Mtaa:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow px-2"></div>
                </div>

                <div class="flex items-baseline">
                    <span class="font-bold w-48 shrink-0">• Jina la Kanda/Kata:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow px-2"></div>
                </div>
            </div>
        </div>

        <!-- SECTION 2 -->
        <div class="mb-8">
            <h4 class="text-lg font-bold text-green-800 uppercase mb-4">2. ELIMU NA UJUZI:</h4>
            <div class="space-y-4 text-lg">
                <div class="flex items-end">
                    <span class="mr-2">• Taja kiwango cha juu cha Elimu ulichonacho:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow"></div>
                </div>
                <div class="flex items-end">
                    <span class="mr-2">• Jina la shule/chuo ulichosoma na mwaka ulipohitimu/kuacha:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow"></div>
                </div>
                <div class="flex items-end">
                    <span class="mr-2">• Taja ujuzi ulionao:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow"></div>
                </div>
                <div class="flex items-end">
                    <span class="mr-2">• Eleza ulipata wapi ujuzi na ni kwa muda gani:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow"></div>
                </div>
            </div>
        </div>

        <!-- SECTION 3 -->
        <div class="mb-8">
            <h4 class="text-lg font-bold text-green-800 uppercase mb-4">3. MAELEZO YA MZAZI/MLEZI /MDHAMINI</h4>
            <div class="space-y-3 text-lg">
                <div class="flex items-baseline">
                    <span class="font-bold w-40 shrink-0">• Jina Kamili:</span>
                    <div
                        class="border-b-2 border-dotted border-gray-400 flex-grow font-mono text-blue-900 px-2 uppercase">
                        {{ $student->parent_name }}
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="font-bold w-40 shrink-0">• Namba ya Simu:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow font-mono text-blue-900 px-2">
                        {{ $student->parent_phone }}
                    </div>
                </div>
                <div class="flex items-baseline">
                    <span class="font-bold w-40 shrink-0">• Uhusiano na mwombaji:</span>
                    <div class="border-b-2 border-dotted border-gray-400 flex-grow px-2"></div>
                </div>
            </div>
        </div>

        <!-- SIGNATURES (Added for completeness based on typical forms) -->
        <div class="mt-16 flex justify-between px-8 text-lg">
            <div class="text-center">
                <div class="w-48 border-b-2 border-dotted border-gray-500 mb-2"></div>
                <p>Saini ya Mwombaji</p>
            </div>
            <div class="text-center">
                <div class="w-48 border-b-2 border-dotted border-gray-500 mb-2"></div>
                <p>Saini ya Mzazi/Mlezi</p>
            </div>
        </div>

    </div>

</body>

</html>