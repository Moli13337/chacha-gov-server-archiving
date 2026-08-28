<template>
	<div class="footer-container">
		<footer class="footer">
			<div class="footer-content">
				<img
					class="ic-party"
					src="~assets/images/ic_party@2x.png"
				>
				<img
					class="ic-police"
					src="~assets/images/ic-police@2x.png"
				>
				<img
					class="ic_jiucuo"
					src="~assets/images/ic_jiucuo@2x.png"
				>
				<div class="text-content">
					<!-- <p>主办：成都市温江区人民政府</p>
					<p>承办：成都市温江区人民政府办公室</p>
					<p>网站标识码5101150001 蜀ICP备19031301号</p>
					<p>技术支持：成都市温江区大数据和电子政务管理办公室</p> -->
					<p>蜀ICP备15009922号-5</p>
				</div>
			</div>
			<div class="copy-right-container">
				<div class="copy-right-center">
					<img
						class="ic-national_emblem"
						src="../../assets/images/ic-national_emblem@2x.png"
					>
					<span class="copy-right">公安机关备案号 51011502000242号</span>
				</div>
			</div>
		</footer>
	</div>
</template>
<script>
export default {
	methods: {
		// handelBackHome() {
		// 	this.$router.push({name: 'index'});
		// },
	}
};
</script>
<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.footer {
  width: 100%;
  background: @backGroundColor;
  .footer-content {
    width: 650px;
    height: 150px;
    margin: auto;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    .ic-party {
      width: 48px;
      height: 52px;
    }
    .ic-police {
      width: 59px;
      height: 60px;
    }
    .ic_jiucuo {
      width: 110px;
      height: 55px;
      margin-right: 44px;
    }
    .text-content {
      color: @textColor;
      font-family: Microsoft YaHei;
      font-size: 14px;
    }
  }
}
.copy-right-container {
  width: 100%;
  height: 54px;
  background: #f8f8f8;
  padding-top: 16px;
  .copy-right-center {
    width: 300px;
    margin: auto;
    display: flex;
    justify-content: space-around;
    .ic-national_emblem {
      width: 20px;
      height: 20px;
    }
    .copy-right {
      font-size: 14px;
      font-family: Microsoft YaHei;
      color: @textColor;
    }
  }
}
</style>

