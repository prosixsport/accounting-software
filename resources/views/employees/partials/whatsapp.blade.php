@php
    $waRaw = trim((string) ($employee->phone ?? ''));
    $waNumber = preg_replace('/\D/', '', $waRaw);
    if (str_starts_with($waNumber, '00')) {
        $waNumber = substr($waNumber, 2);
    }
    // Pakistan local mobile numbers become international numbers.
    if (preg_match('/^03\d{9}$/', $waNumber)) {
        $waNumber = '92'.substr($waNumber, 1);
    } elseif (preg_match('/^3\d{9}$/', $waNumber)) {
        $waNumber = '92'.$waNumber;
    }
    $waValid = (bool) preg_match('/^[1-9]\d{7,14}$/', $waNumber);
    // Do not guess international country codes for ambiguous local numbers.
    $waInternational = str_starts_with($waRaw, '+') || str_starts_with($waRaw, '00')
        || preg_match('/^92\d{10}$/', $waNumber);
@endphp
@if($waValid && $waInternational)
<a href="https://web.whatsapp.com/send?phone={{ $waNumber }}" target="prosix_employee_whatsapp" data-employee-wa-chat
   class="employee-action-btn employee-whatsapp-action"
   style="color:#15803d;background:#ecfdf5;border:1px solid #bbf7d0"
   title="Chat with {{ $employee->name }} on WhatsApp"
   aria-label="Chat with {{ $employee->name }} on WhatsApp">
    <i class="bi bi-whatsapp" aria-hidden="true"></i>
</a>
@endif
@once
<style>
.employee-whatsapp-action{display:inline-flex;align-items:center;justify-content:center;min-width:34px;min-height:34px;text-decoration:none;border-radius:7px}
.employee-whatsapp-action:hover{background:#dcfce7!important}
</style>
<script>
(()=>{
    if(window.prosixWaChatInstalled)return;
    window.prosixWaChatInstalled=true;
    let chatWindow=null;
    document.addEventListener('click',function(event){
        const link=event.target.closest('[data-employee-wa-chat]');
        if(!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey)return;
        event.preventDefault();
        if(chatWindow && !chatWindow.closed){
            try{
                chatWindow.location.href=link.href;
                chatWindow.focus();
                return;
            }catch(error){
                alert('Browser cannot reuse the WhatsApp tab. Close that tab, then click the employee again.');
                return;
            }
        }
        chatWindow=window.open(link.href,'prosix_employee_whatsapp');
        if(!chatWindow){
            alert('Allow pop-ups for this software, then click WhatsApp again.');
            return;
        }
        chatWindow.focus();
    });
})();
</script>
@endonce
