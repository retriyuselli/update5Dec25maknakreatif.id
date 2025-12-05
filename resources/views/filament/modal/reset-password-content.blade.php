{{-- Custom content for reset password modal --}}
<div 
    x-data="{ 
        mounted: false 
    }"
    x-init="
        $nextTick(() => {
            mounted = true;
            // Style the submit button
            const submitBtn = document.querySelector('.fi-modal-footer button[type=submit]');
            if (submitBtn) {
                submitBtn.classList.remove('fi-btn-color-gray');
                submitBtn.classList.add('fi-btn-color-purple');
                submitBtn.style.backgroundColor = 'rgb(147 51 234)';
                submitBtn.style.borderColor = 'rgb(147 51 234)';
                submitBtn.style.color = 'white';
            }
        })
    "
>
    <style>
        .fi-modal-footer .fi-btn[type='submit'] {
            background-color: rgb(147 51 234) !important;
            border-color: rgb(147 51 234) !important;
            color: white !important;
        }
        
        .fi-modal-footer .fi-btn[type='submit']:hover {
            background-color: rgb(126 34 206) !important;
            border-color: rgb(126 34 206) !important;
        }
    </style>
</div>
