@extends('layout/main')

@section('content')
    <div class="content-wrapper">
        <div class="p-3"> <!-- Menambahkan padding untuk menggeser isi ke dalam -->
            <p class="p-3 mb-2 font-weight-bold h3">Data Absensi Karyawan</p>
            <div class="p-3 ml-3 text-black bg-white"> <!-- Menambahkan padding dan margin -->
                <table id="myTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NPK</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Waktu Check in</th>
                            <th>Waktu Check out</th>
                        </tr>


                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('absensiControllerAjax.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    },
                    {
                        data: 'npk',
                        name: 'npk',
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    },
                    {
                        data: 'waktuci',
                        name: 'waktuci',
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    },
                    {
                        data: 'waktuco',
                        name: 'waktuco',
                        className: 'p-1 text-sm text-gray-700 whitespace-nowrap'
                    }
                ]
            });
        });
    </script>
@endsection
