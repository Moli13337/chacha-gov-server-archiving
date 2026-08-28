<template>
	<div class="inner-detail-container">
		<div
			v-if="!isHasDetail"
			class="empty-box"
		>
			<empty :tip="tip"/>
		</div>

		<div v-else>
			<div class="title-box">
				<div class="title">
					<p class="text-box">{{detail.title}}</p>
					<div
						class="btn-box"
						v-if="isSubmiteOption && !detail.link"
					>
						<el-button
							type="primary"
							@click="handleSumitClick(detail.id)"
						>
							提交意见
						</el-button>
					</div>
				</div>
				<div class="tip-box">
					<p class="item-tip"><span>发布日期: </span><span>{{detail.publish_time | formatDate}}</span></p>
					<p class="item-tip"><span>来源: </span><span>{{detail.source_name}}</span></p>
				</div>
			</div>
			<el-divider></el-divider>
			<div class="content-box">
				<p><el-divider direction="vertical"></el-divider><span class="item-title">内容正文</span></p>
				<div
					class="item-content description-content"
					v-html="detail.content"
				>
				</div>
				<div v-if="detail.file && detail.file.length">
					<p><el-divider direction="vertical"></el-divider><span class="item-title">资料下载</span></p>
					<div class="item-content download-box">
						<p
							v-for="(item, index) in  detail.file"
							:key="index"
						>
							<a
								@click="downloadFile(item.save_url)"
							><i class="el-icon-document"></i>{{item.file_name || item.name}}
							</a>
						</p>
					</div>
				</div>
				<div v-if="isSubmiteOption && detail.link">
					<p><el-divider direction="vertical"></el-divider><span class="item-title">提交意见入口</span></p>
					<div class="item-content download-box">
						<a
							target="_blank"
							:href="detail.link"
						>{{detail.link}}
						</a>
					</div>
				</div>
				<div v-if="isInformation && detail.link">
					<p><el-divider direction="vertical"></el-divider><span class="item-title">关联链接</span></p>
					<div class="item-content download-box">
						<a
							target="_blank"
							:href="detail.link"
						>{{detail.link}}
						</a>
					</div>
				</div>
			</div>
		</div>
		<SubmitOptionDialog
			:submitVisible = "submitVisible"
			@changeSubmitVisible="changeSubmitVisible"
			:captcha="captcha"
		/>
	</div>
</template>
<script>

import download from '@/utils/download';
import SubmitOptionDialog from '../components/butler/submit_option_dialog.vue';
import empty from '../components/empty';
import {
	FETCH_CAPTCHACODE
} from '@/utils/urls.js';
export default {
	mixins: [download],
	components: {
		SubmitOptionDialog,
		empty
	},
	props: {
		title: {
			type: String,
			default: ''
		},
		detail: {
			type: Object,
			default: function () {
				return {};
			}
		},
		tip: {
			type: String,
			default: '暂无数据信息'
		}
	},
	data() {
		return {
			currendId: 0,
			submitVisible: false,
			captcha: {
				pictureCode: '',
				captchaKey: ''
			}

		};
	},
	computed: {
		publishtime() {
			return this.detail.created_at || this.detail.publish_time;
		},
		isHasDetail() {
			return (this.detail && this.detail.id) || this.detail.length;
		},
		isSubmiteOption() {
			return this.$route.query.type == 'option';
		},
		isInformation() {
			return this.$route.query.type == 'information';
		}
	},
	methods: {
		handleSumitClick(id) {
			this.currendId = id;
			this.submitVisible = true;
			// 需要获取一下验证码
			this.fetchPictureCode();
		},
		changeSubmitVisible(val) {
			this.submitVisible = val;
		},
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE)
				.then(({img, key}) => {
					this.captcha.pictureCode = img;
					this.captcha.captchaKey = key;
				}).catch(error => {
					console.log(error.message);
				});
		},
	}
};
</script>
<style lang="less">
@import '~assets/css/common_avairail.less';
.inner-detail-container {
    border:1px solid @defaultBorderColor;
    padding: 30px 40px;
    background: #ffffff;
    min-height: 500px;
  img {
    display: inline-block;
  }
  .el-divider--vertical {
      width: 6px;
      height: 27px;
      background: @primaryColor;
    }
    .title {
      text-align: center;
      font-size:23px;
      font-weight:400;
      display: flex;
      justify-content: space-between;
      align-items: center;
      .text-box {
        flex: 1;
      }
      .btn-box {
        width: 150px;
        text-align: center;
      }
    }
    .tip-box {
      padding: 10px 50px 0 50px;
      display: flex;
      justify-content: space-around;
      .item-tip {
        color: #B5B5B5;
        font-size:14px;
      }
    }
    .item-title {
      font-size:19px;
      font-weight:400;
      color: #3B3B3B;
    }
    .item-content {
      font-size:16px;
      color: #818181;
      text-indent:32px;
      padding: 20px 0;
       width: 100%;
      table {
        border: 1px solid @borderLine !important;
        border-collapse:collapse;
        margin: 20px 0px;
        width: 100% !important;
        td {
          border: 1px solid @borderLine !important;
        }
      }
    }
    .description-content {
      overflow-x: scroll; /*添加横向滚动条*/
    }
    .download-box {
      padding-left: 20px;
      text-indent:0px;
      color: #3895F1;
      p {
        line-height: 30px;
      }
    }
    .content-box {
      display: block;
    }
}
.empty-box {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction:column;
}


</style>
