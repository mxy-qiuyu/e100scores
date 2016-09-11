var Config = {
  TIME_FORMAT: 'yyyy-MM-dd hh:mm:ss'
}

// 对Date的扩展，将 Date 转化为指定格式的String   
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，   
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)   
// 例子：   
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423   
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18   
Date.prototype.Format = function(fmt)   
{ //author: meizz   
  var o = {   
    "M+" : this.getMonth()+1,                 //月份   
    "d+" : this.getDate(),                    //日   
    "h+" : this.getHours(),                   //小时   
    "m+" : this.getMinutes(),                 //分   
    "s+" : this.getSeconds(),                 //秒   
    "q+" : Math.floor((this.getMonth()+3)/3), //季度   
    "S"  : this.getMilliseconds()             //毫秒   
  };   
  if(/(y+)/.test(fmt))   
    fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));   
  for(var k in o)   
    if(new RegExp("("+ k +")").test(fmt))   
  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));   
  return fmt;   
} 

/**
* (改编自网上方法)返回两个"yyyy-MM-dd hh:mm:ss"字符串日期的秒数差
* 计算结果为endTime-beginTime
*/
function timeDiff(beginTime, endTime) {
    if (beginTime == ''){
      beginTime = '2016-06-11 00:00:00';
    }
    if (endTime == ''){
      endTime = '2016-06-11 00:00:00';
    }
    var beginTimes = beginTime.substring(0, 10).split('-');
    var endTimes = endTime.substring(0, 10).split('-');

    beginTime = beginTimes[1] + '/' + beginTimes[2] + '/' + beginTimes[0] + ' ' + beginTime.substring(10, 19);
    endTime = endTimes[1] + '/' + endTimes[2] + '/' + endTimes[0] + ' ' + endTime.substring(10, 19);

    var diff = (Date.parse(endTime) - Date.parse(beginTime)) / 1000;
    return diff;
}