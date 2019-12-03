new Swal({
    text: "Apakah Anda ingin menghapus data ini?",
    type: "question",   
    showCloseButton: true,
    showCancelButton: true,
    cancelButtonText: "Batal",
    customClass: {
        confirmButton: 'bg-danger',
        cancelButton: 'bg-primary'
    },
})
.then((result) => {
  if (result.value) {
        @stack('yesdelete')
  }
})