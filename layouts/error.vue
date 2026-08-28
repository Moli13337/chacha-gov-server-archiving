<template>
	<div class="erro-container">
		<div class="erro-box">
			<img src="../assets/images/empty.png"/>
			<div
				v-if="error.statusCode === 404"
				class="erro-tip"
			>
				<p>很抱歉，您访问的页面不存在</p>
				<p>可能原因:</p>
				<ul class="tips">
					<li>1.您访问的链接已过期。</li>
					<li>2.在地址栏输入了错误地址。</li>
					<li>
						3.去
						<nuxt-link to="/">
							<span class="toHome">首页</span>
						</nuxt-link>
					</li>
				</ul>
			</div>
			<div
				v-else-if="error.statusCode !== 13008"
				class="erro-tip"
			>
				<p>很抱歉，应用发生错误异常</p>
				<p>您可以:</p>
				<ul class="tips">
					<li>1.检查访问的网页是否正确。</li>
					<li>
						2.去
						<nuxt-link to="/">
							<span class="toHome">首页</span>
						</nuxt-link>
					</li>
				</ul>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: ['error'],
	layout: 'empty'
};
</script>
<style lang="less">
@import "~assets/css/common_avairail";
.erro-container {
  width: 100%;
  min-height: 500px;
  min-height: 100vh;
  background: @backGroundColor;
  .erro-box {
    width: 400px;
    padding-top: 100px;
    margin: auto;
    .erro-tip {
      font-size: 18px;
      font-family: Microsoft YaHei;
      font-weight: bold;
      padding-left: 50px;
    }
    .tips {
      font-weight: 300px;
      font-size: 16px;
      color: @menuDefaultTextColor;
    }
    .toHome {
      color: @poliyItemColor;
      text-decoration: underline;
    }
    .toHome:hover {
      color: @primaryColor;
    }
  }
}
</style>

