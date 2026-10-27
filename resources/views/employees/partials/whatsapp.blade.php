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
   data-wa-url="{{ route('employees.whatsapp.send', $employee) }}" data-employee-whatsapp data-wa-number="{{ $waNumber }}" data-wa-name="{{ $employee->name }}">
    <i class="bi bi-whatsapp" aria-hidden="true"></i>
</a>
@endif
@once
<dialog id="employee-wa-dialog" style="width:min(540px,94vw);max-height:90dvh;overflow:auto;border:1px solid #dbe5e8;padding:24px;border-radius:12px">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div><h4 class="mb-1">WhatsApp Message</h4><div id="employee-wa-person" class="text-muted"></div></div>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-wa-close aria-label="Close WhatsApp message">Close</button>
    </div>
    <label for="employee-wa-text" class="form-label">Message</label>
    <textarea id="employee-wa-text" class="form-control" rows="5" maxlength="4000" placeholder="Write your message. Enter to send; Shift+Enter for a new line."></textarea>
    <p class="small text-muted mt-3">Sent from the configured company WhatsApp number. For custom text, the employee must have messaged this number within the last 24 hours.</p>
    <div id="employee-wa-result" role="status" aria-live="polite" class="small mb-3"></div>
    <button type="button" id="employee-wa-send" class="btn btn-success">Send Message</button>
</dialog>
<style>#employee-wa-dialog::backdrop{background:#102a435c}.employee-whatsapp-action{display:inline-flex;align-items:center;justify-content:center;min-width:34px;min-height:34px;text-decoration:none;border-radius:7px}.employee-whatsapp-action:hover{background:#dcfce7!important}</style>
<script>
(()=>{
    const dialog=document.getElementById('employee-wa-dialog');
    const text=document.getElementById('employee-wa-text');
    const send=document.getElementById('employee-wa-send');
    const result=document.getElementById('employee-wa-result');
    let url='',busy=false;
    document.addEventListener('click',event=>{
        const trigger=event.target.closest('[data-employee-whatsapp]');
        if(!trigger)return;
        event.preventDefault();
        if(busy)return;
        url=trigger.dataset.waUrl;
        document.getElementById('employee-wa-person').textContent=trigger.dataset.waName+' · +'+trigger.dataset.waNumber;
        text.value='';result.textContent='';dialog.showModal();text.focus();
    });
    async function submit(){
        if(busy || !text.value.trim())return;
        busy=true;send.disabled=true;text.disabled=true;send.textContent='Sending…';result.textContent='';
        try{
            const response=await fetch(url,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':@json(csrf_token())},body:JSON.stringify({message:text.value.trim()})});
            const data=await response.json().catch(()=>({message:response.status===419?'Session expired. Refresh and log in again.':'Request failed. Check your connection/session.'}));
            result.textContent=data.message || 'Request failed.';
            result.className='small mb-3 '+(response.ok?'text-success':'text-danger');
            if(response.ok)text.value='';
        }catch(e){result.textContent='Connection interrupted. Check WhatsApp before sending again.';result.className='small mb-3 text-danger';}
        finally{busy=false;send.disabled=false;text.disabled=false;send.textContent='Send Message';text.focus();}
    }
    send.addEventListener('click',submit);
    text.addEventListener('keydown',event=>{if(event.key==='Enter'&&!event.shiftKey&&!event.isComposing){event.preventDefault();submit();}});
    dialog.querySelector('[data-wa-close]').addEventListener('click',()=>{if(!busy)dialog.close();});
    dialog.addEventListener('cancel',event=>{if(busy)event.preventDefault();});
})();
</script>
@endonce
