@extends('admin.layouts.app')
@section('title', 'All Customer')
@section('content')
  <div class="page-content">
    <div class="container-fluid">

      <!-- start page title -->
      <div class="row">
        <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">All Customer</h4>
          </div>
        </div>
      </div>
      <!-- end page title -->

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <a href="{{ route('customer.create') }}" class="btn btn-dark btn-rounded waves-effect waves-light"
                style="float:right;"><i class="fas fa-plus-circle"> Add Customer </i></a> <br> <br>

              <h4 class="card-title">All Customer Data  </h4>


              <table id="datatable" class="table table-bordered dt-responsive nowrap"
                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <thead>
                  <tr>
                    <th>Sl</th>
                    <th>Name</th>
                    <th>Image </th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Action</th>

                </thead>


                <tbody>

                  @foreach ($customers as $key => $item)
                    <tr>
                      <td> {{ $key + 1 }} </td>
                      <td> {{ $item->name }} </td>
                      <td> <img src="{{ asset($item->customer_image) }}" style="width:60px; height:50px"> </td>
                      <td> {{ $item->email }} </td>
                      <td> {{ $item->address }} </td>
                      <td>
                        <a href="{{ route('customer.edit', $item->id) }}" class="btn btn-info sm" title="Edit Data"> <i
                            class="fas fa-edit"></i> </a>

                        <a data-id="{{ $item->id }}" href="{{ route('customer.destroy', $item->id) }}"
                          class="btn btn-danger sm" title="Delete Data delete" id="delete"> <i
                            class="fas fa-trash-alt"></i> </a>


                        <form id="delete-form" action="" method="POST">
                          @csrf
                          @method('DELETE')
                        </form>

                      </td>

                    </tr>
                  @endforeach

                </tbody>
              </table>

            </div>
          </div>
        </div> <!-- end col -->
      </div> <!-- end row -->



    </div> <!-- container-fluid -->
  </div>


@endsection
@push('page-js')
  <script>
    $(function() {
      $(document).on("click", "#delete", function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
        var id = $(this).data('id'); // Get the value of data-id attribute
        Swal.fire({
          title: "Are you sure?",
          text: "Delete This Data?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.isConfirmed) {
            $('#delete-form').attr('action', link); // Set the form action attribute
            $('#delete-form').submit();
            Swal.fire("Deleted!", "Your file has been deleted.", "success");
          }
        });
      });
    });
  </script>
@endpush
