<script type="text/javascript">
var Toast;

Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
});

@if($type == 'success')
      Toast.fire({
        type: 'success',
        title: '{{ $text }}',
        background: 'green',
        customClass: {
          content: 'bg-success',
          title: 'text-light',          
        }
      });
@elseif ($type == 'info')
      Toast.fire({
        type: 'info',
        title: '{{ $text }}',
        background: 'blue',
        customClass: {
          content: 'bg-primary',          
          title: 'text-light',          
        }
      });
@elseif ($type == 'error')
      Toast.fire({
        type: 'error',
        title: '{{ $text }}',
        background: "red",
        customClass: {
          content: 'bg-danger',          
          title: 'text-light',          
        }
      });
@elseif ($type == 'warning')
      Toast.fire({
        type: 'warning',
        title: '{{ $text }}',        
        customClass: {
          content: 'bg-warning',
          title: 'text-danger'
        }
      });
@endif
</script>