<template>
	<div>
		<ul class="tips-container">
			<li
				class="tip-item"
				v-for="(item, index) in listContent"
				:key="index"
				@click="handleGuide(item)"
			>{{item}}</li>
			<li
				v-if="retract"
				class="tip-item tip-retract"
				@click="handlePackUp"
			><i class="el-icon-arrow-up"></i><br/>收起
			</li>
			<li
				v-if="open"
				class="tip-item tip-retract"
				@click="handleOpen"
			><i class="el-icon-arrow-down"></i><br/>展开
			</li>
		</ul>
		<Consultation
			:visible.sync="dialog.dialogFormVisible"
			:tipType="dialog.tipType"
			:tip="dialog.tip"
			:name="dialog.name"
			:title="dialog.title"
			:contentTitle="dialog.contentTitle"
			:picturCode="picturCode"
			:captchaKey="captchaKey"
			v-on:changeVisible="changeVisible"
			@fetchPictureCode ="fetchPictureCode"
		/>
	</div>

</template>
<script>
import Consultation from './consultation';
import storage from '../utils/storage';
import {
	FETCH_CAPTCHACODE
} from '@/utils/urls';
export default {
	components: {
		Consultation
	},
	data() {
		return {
			retract: true,
			open: false,
			listContent: ['我要咨询', '我要建议', '我要投诉', '新手指南'],
			picturCode: '',
			captchaKey: '',
			dialog: {
				dialogFormVisible: false,
				tipType: 3,
				tip: '',
				title: '',
				content: '',
				name: '',
				contentTitle: ''
			}
		};
	},

	methods: {
		// 新手指南
		handleGuide(item) {
			switch (item) {
				case '我要咨询':
					if (storage.getItem('token')) {
						this.showDailog(3, '我要咨询', '咨询标题', '咨询内容');
					} else {
						// this.$router.push({name: 'login'});
						this.tencentLogin();
					}
					break;
				case '我要投诉':
					if (storage.getItem('token')) {
						// this.showDailog(2, '我要投诉', '投诉标题', '投诉内容');
						this.$router.push({name: 'index-complaint'});
					} else {
						// this.$router.push({name: 'login'});
						this.tencentLogin();
					}
					break;
				case '我要建议':
					if (storage.getItem('token')) {
						this.showDailog(1, '我要建议', '建议标题', '建议内容');
					} else {
						// this.$router.push({name: 'login'});
						this.tencentLogin();
					}
					break;
				case '新手指南':
					this.$router.push({name: 'index-guide', query: {guideType: 1}});
					break;
				default:
			}
		},
		showDailog(type, name, title, contentTitle) {
			this.dialog.tipType = type;
			this.dialog.name = name;
			this.dialog.title = title;
			this.dialog.contentTitle = contentTitle;
			this.dialog.dialogFormVisible = true;
		},
		// 收起
		handlePackUp() {
			this.listContent = [];
			this.retract = false;
			this.open = true;
		},
		// 展开
		handleOpen() {
			this.listContent = ['我要咨询', '我要建议', '我要投诉', '新手指南'],
			this.retract = true;
			this.open = false;
		},
		changeVisible(newV) {
			this.dialog.dialogFormVisible = newV;
		},
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					console.log(img);
					console.log(key);
					this.pictureCode = img;
					this.captchaKey = key;
				});
		},
	},
	mounted() {
		// 初始化图片验证码，返回data数据
		this.$axios.get(FETCH_CAPTCHACODE).then(({img, key}) => {
			{
				// 图形验证码图片地址
				this.picturCode = img,
				// 图形验证码key值
				this.captchaKey = key;
			}
		});
	}

};
</script>
<style lang="less" scoped>
@import "~assets/css/common_avairail.less";
  .tips-container {
    width: 52px;
    height: 300px;
    .tip-item {
      width: 100%;
      height: 52px;
      color: @backGroundColor;
      background: @primaryColor;
      margin-bottom: 10px;
      text-align: center;
      padding: 5px;
      cursor: pointer;
    }
    .tip-retract {
       background: @backGroundColor;
       border: 1px solid @primaryColor;
       color: @primaryColor;
    }
  }
</style>

