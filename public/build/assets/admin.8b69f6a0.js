let a=document.getElementsByName("emission[duration]")[0];a&&a.addEventListener("change",t=>{let e=t.target.value.split(".");e[1]>59&&e[1]<89?(e[0]++,e[1]=0,t.target.value=e.concat()):e[1]>88&&(e[1]=59,t.target.value=e.concat())});