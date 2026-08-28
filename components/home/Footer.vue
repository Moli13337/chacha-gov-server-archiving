<template>
	<div class="footer-container">
		<footer class="footer">
			<div class="tips-container">
				<ul class="tip-items">
					<li
						class="tip-item"
						v-for="(item, index) in tipList"
						:key="index"
						:class="{
							'active': isActive && active == index
						}"
						@click="handleActive(index, item)"
					>{{item.tip}}</li>
				</ul>
			</div>
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
					<p>承办：成都市温江区人民政府办公室</p> -->
					<!-- <p>网站标识码5101150001 蜀ICP备19031301号</p> -->
					<p>蜀ICP备15009922号-5</p>
					<!-- <p>技术支持：成都市温江区大数据和电子政务管理办公室</p> -->
				</div>
			</div>
			<!-- <div class="copy-right-container">
				<div class="copy-right-center">
					<img
						class="ic-national_emblem"
						src="~assets/images/ic-national_emblem@2x.png"
					>
					<span class="copy-right">公安机关备案号 51011502000242号</span>
				</div>
			</div> -->
		</footer>
	</div>
</template>
<script>
export default {
	data() {
		return {
			active: -1,
			path: '',
			isActive: false,
			route: [
				'/footnote', '/footnote/statement', '/footnote/concat'
			],
			tipList: [
				// {
				// 	tip: '关于我们'
				// },
				// {
				// 	tip: '网站地图'
				// },
				// {
				// 	tip: '法律申明'
				// },
				{
					tip: '联系我们'
				},
				// {
				// 	tip: '相关链接'
				// },
			]
		};
	},

	methods: {
		handleActive(index, item) {
			this.active = index;
			if (item.tip == '关于我们') {
				this.$router.push({name: 'index-footnote'});
			} else if (item.tip == '法律申明') {
				this.$router.push({name: 'index-footnote-statement'});
			} else if (item.tip == '联系我们') {
				this.$router.push({name: 'index-footnote-concat'});
			}
		},
		isActiveFn() {
			this.isActive = this.route.indexOf(this.$route.path) > 0;
		}
	},
	mounted() {
		this.path = this.$route.path;
		// console.log('w3333', this.route.indexOf(this.path) != -1);
	},
	watch: {
		$route() {
			// console.log('path', this.path);
			this.isActiveFn();
			// console.log('isActive', this.isActive);
		}
	}
};
</script>
<style lang="less">
@import "~assets/css/common_avairail.less";
.footer {
  width: 100%;
  background: @backGroundColor;
  margin-top: 20px;
  .tips-container {
    height: 86px;
    border-bottom: 1px solid #E9E9E9;
    .tip-items {
      cursor: pointer;
      width: 400px;
      height: 100%;
      margin: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #818181;
      .tip-item {
        width: 150px;
        text-align: center;
        border-right: 1px solid #707070;
        padding: 0 10px;
        font-size:16px;
        font-family:Microsoft YaHei;
        font-weight:bold;
      }
      .tip-item:last-child {
        border-right: none;
      }
      .active {
        color: @primaryColor;
      }
    }
  }
  .footer-content {
    width: 650px;
    height: 150px;
    margin: auto;
    display: flex;
    justify-content: center;
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

