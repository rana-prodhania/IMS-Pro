 <div class="vertical-menu">

   <div data-simplebar class="h-100">

     <!-- User details -->


     <!--- Sidemenu -->
     <div id="sidebar-menu">
       <!-- Left Menu Start -->
       <ul class="metismenu list-unstyled" id="side-menu">
         <li class="menu-title">Menu</li>

         <li>
           <a href="{{ url('/dashboard') }}" class="waves-effect">
             <i class="ri-home-fill"></i>
             <span>Dashboard</span>
           </a>
         </li>

         <li>
           <a href="javascript: void(0);" class="has-arrow waves-effect">
             <i class="ri-hotel-fill"></i>
             <span>Manage Suppliers</span>
           </a>
           <ul class="sub-menu" aria-expanded="false">
             <li><a href="{{ route('supplier.index') }}">All Supplier</a></li>

           </ul>
         </li>

         <li>
           <a href="javascript: void(0);" class="has-arrow waves-effect">
             <i class="ri-shield-user-fill"></i>
             <span>Manage Customers</span>
           </a>
           <ul class="sub-menu" aria-expanded="false">
             <li><a href="{{ route('customer.index') }}">All Customers</a></li>
             {{-- <li><a href="{{ route('credit.customer') }}">Credit Customers</a></li>
             <li><a href="{{ route('paid.customer') }}">Paid Customers</a></li>
             <li><a href="{{ route('customer.wise.report') }}">Customer Wise Report</a></li> --}}

           </ul>
         </li>









       </ul>
     </div>
     <!-- Sidebar -->
   </div>
 </div>
