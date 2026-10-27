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
<a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
   class="employee-action-btn employee-whatsapp-action" style="color:#15803d;background:#ecfdf5;border:1px solid #bbf7d0"
   title="Open WhatsApp chat with {{ $employee->name }}" aria-label="WhatsApp {{ $employee->name }}"
   data-employee-whatsapp data-wa-number="{{ $waNumber }}" data-wa-name="{{ $employee->name }}">
    <i class="bi bi-whatsapp" aria-hidden="true"></i>
</a>
@endif
@once
<dialog id="employee-wa-dialog" style="width:min(540px,94vw);max-height:90dvh;overflow:auto;border:1px solid #dbe5e8;padding:24px;border-radius:12px">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div><h4 class="mb-1">WhatsApp Message</h4><div id="employee-wa-person" class="text-muted"></div></div>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-wa-close aria-label="Close WhatsApp message">Close</button>
    </div>
    <label for="employee-wa-text" class="form-label">Message (optional)</label>
    <textarea id="employee-wa-text" class="form-control" rows="5" maxlength="4000" placeholder="Write a message or open the chat directly..."></textarea>
    <p class="small text-muted mt-3">Opens WhatsApp using the account signed in on this device. Press Send there. Voice notes, photos and documents can be sent inside WhatsApp. A saved number does not confirm a WhatsApp account.</p>
    <a id="employee-wa-open" class="btn btn-success" href="https://web.whatsapp.com/" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Open WhatsApp Chat</a>
</dialog>
<style>#employee-wa-dialog::backdrop{background:#102a435c}.employee-whatsapp-action{display:inline-flex;align-items:center;justify-content:center;min-width:34px;min-height:34px;text-decoration:none;border-radius:7px}.employee-whatsapp-action:hover{background:#dcfce7!important}</style>
<script>
(()=>{
    const dialog=document.getElementById('employee-wa-dialog');
    const text=document.getElementById('employee-wa-text');
    const open=document.getElementById('employee-wa-open');
    let number='';
    function updateLink(){open.href='https://wa.me/'+number+(text.value.trim()?'?text='+encodeURIComponent(text.value.trim()):'');}
    document.addEventListener('click',event=>{
        const trigger=event.target.closest('[data-employee-whatsapp]');
        if(!trigger || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey)return;
        if(typeof dialog.showModal!=='function')return;
        event.preventDefault();number=trigger.dataset.waNumber;
        document.getElementById('employee-wa-person').textContent=trigger.dataset.waName+' · +'+number;
        text.value='';updateLink();dialog.showModal();text.focus();
    });
    text.addEventListener('input',updateLink);
    dialog.querySelector('[data-wa-close]').addEventListener('click',()=>dialog.close());
})();
</script>
@endonce
