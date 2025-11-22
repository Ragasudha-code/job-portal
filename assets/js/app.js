// assets/js/app.js
document.addEventListener('DOMContentLoaded', function(){
  const draftCheckbox = document.getElementById('save_draft');
  if(draftCheckbox){
    draftCheckbox.addEventListener('change', function(){
      const submitBtn = document.getElementById('submit_btn');
      if(this.checked) submitBtn.innerText = 'Save Draft';
      else submitBtn.innerText = 'Submit Application';
    });
  }
});
