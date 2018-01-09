<?php

function success($Content,$Action = '',$arr='')
{
	if ($Action == '') {
		$Action = $_SERVIER['HTTP_REFERER'];
	} else {
		$Action = U($Action);
	}
	echo '<script> alert("'.$Content.'");location.href="'.$Action.'"</script>';

}

/**
 * 成功跳转
 * @Author   ryan
 * @DateTime 2017-11-20
 * @email    931035553@qq.com
 * @return   [type]           [description]
 */

function error($Content,$Action = '',$arr='')
{
	if ($Action == '') {
		$Action = 'history.go(-1)';
	} else {
		$Action ='location.href="'.U($Action,$arr).'"';
	}

	echo '<script> alert("'.$Content.'");'.$Action.';</script>';
}

/**
 * 专用于返回上上次页面的跳转
 * @param  [type] $Content [description]
 * @param  string $Action  [description]
 * @param  string $arr     [description]
 * @return [type]          [description]
 */
function success_goBack($Content,$Action = '',$arr='')
{
	if ($Action == '') {
		$Action = $_SERVIER['HTTP_REFERER'];
	}
	echo '<script> alert("'.$Content.'");location.href="'.$Action.'"</script>';

}