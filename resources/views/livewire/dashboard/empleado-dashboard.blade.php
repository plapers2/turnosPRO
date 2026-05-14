{{--
    EmpleadoDashboard usa exactamente la misma vista que AdminDashboard.
    La diferencia de datos ya está encapsulada en EmpleadoDashboard::baseQuery().
    Este archivo existe solo para que Livewire pueda resolverlo si en algún
    momento necesitás personalizar el layout del empleado sin tocar el del admin.
--}}
@include('livewire.dashboard.admin-dashboard')
