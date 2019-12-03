<div id="modalform" class="modal" tabindex="-1" role="dialog">
  <form id="form">
    <div class="modal-dialog {{ isset($modalsize) ? $modalsize : 'modal-md' }}" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">              
        </div>
        <div class="modal-footer">
          <button type="submit" id="savebutton" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </div>
  </form>
</div>