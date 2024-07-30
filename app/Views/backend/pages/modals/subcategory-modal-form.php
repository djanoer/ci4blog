<div class="modal fade" id="sub-category-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" action="<?= route_to('add-subcategory'); ?>" method="post" id="add_subcategory_form">
      <div class="modal-header">
        <h4 class="modal-title" id="myLargeModalLabel">
          Large modal
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" class="ci_csrf_data">
        <div class="form-group">
            <label for="parent_cat"><b>Parent category</b></label>
            <select name="parent_cat" id="parent_cat" class="form-control">
                <option value="">Uncategorized</option>
            </select>
        </div>
        <div class="form-group">
          <label for="subcategory_name"><b>Sub Category name</b></label>
          <input id="subcategory_name" type="text" class="form-control" name="subcategory_name" placeholder="Enter sub category name">
          <span class="text-danger error-text subcategory_name_error"></span>
        </div>
        <div class="form-group">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" class="form-control" cols="30" rows="10" placeholder="Type..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
        <button type="submit" class="btn btn-primary action">
          Save changes
        </button>
      </div>
    </form>
  </div>
</div>