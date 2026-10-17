<script>
// Existing accounting weeks stay 1–7, 8–14, 15–21, 22–28 and 29–month end.
window.advanceWeekDate=function(month,week){
 const [year,m]=month.split('-').map(Number),start=(Number(week)-1)*7+1;
 const end=Math.min(start+6,new Date(Date.UTC(year,m,0)).getUTCDate());
 const saturday=start+(6-new Date(Date.UTC(year,m-1,start)).getUTCDay()+7)%7;
 const day=saturday<=end?saturday:end;
 return month+'-'+String(day).padStart(2,'0');
};
</script>
