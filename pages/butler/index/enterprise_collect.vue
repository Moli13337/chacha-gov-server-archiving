<template>
	<div class="collect-content">
		<div class="top">
			<p>
				<el-divider direction="vertical"></el-divider>
				<span>企业意见征集</span>
			</p>
		</div>
		<!-- 消息为空 -->
		<Empty v-if="!messageList || !messageList.length"/>
		<div
			class="list-container"
			v-else
		>
			<ul v-loading="loading">
				<li
					class="list-item"
					v-for="(item, index) in messageList"
					:key="index"
				>
					<el-row>
						<el-col
							:span="19"
							class="col-left"
						>
							<div
								class="title"
								@click="handleToOptionDetail(item.id)"
							>
								<div class="message">
									<p class="item-title">【{{item.type_name}}】{{item.title}}</p>
								</div>
							</div>
							<div class="describe">
								<div v-html="richTextToEllipsis(item.content, 80)"></div>
							</div>
							<div class="tip-box">
								<p class="tip">
									来源：
									<span class="tip-content">{{item.source_name}}</span>
								</p>
								<p class="tip">
									发布日期：
									<span class="tip-content">{{item.publish_time | formatDate('YYYY-MM-DD')}}</span>
								</p>
							</div>
						</el-col>
						<el-col
							:span="5"
							class="col-right"
						>
							<div>
								<a
									v-if="item.link"
									:href="item.link"
									target="_blank"
								>
									<el-button type="primary">提交意见</el-button>
								</a>
								<el-button
									v-else
									type="primary"
									@click="handleSumitClick(item.id)"
								>提交意见</el-button>
							</div>
						</el-col>
					</el-row>
				</li>
			</ul>
			<pagination
				:pagination="pagination"
				@onPageChange="onPageChange"
			/>
		</div>
		<el-dialog
			:visible.sync="submitVisible"
			width="45%"
			center
			class="optionForm"
			@close="handleCloseDialog"
		>
			<template slot="title">
				<p
					class="title"
					style="background='red'"
				>企业意见征集</p>
			</template>
			<el-form
				:model="opinionForm"
				:rules="rules"
				ref="optionForm"
			>
				<el-form-item
					label="用户意见"
					label-width="120px"
					prop="content"
				>
					<el-input
						type="textarea"
						:rows="5"
						v-model="opinionForm.content"
						show-word-limit
						autocomplete="off"
						maxlength="500"
					></el-input>
				</el-form-item>
				<el-form-item
					label="反馈文件上传"
					label-width="120px"
				>
					<diy-upload
						v-model="file"
						multiple
						:tip="'支持上传DOC，WPS，DOCX，PDF，XLS，XLSX，JPG， JPEG，PNG，BMP，TXT格式的文件'"
						:limitSizePerFile="100"
						:accept="'.jpg,.JPG,.JPEG,.jpeg,.PNG,.png,.BMP,.bmp,.DOC,.doc,.DOCX,.docx,.WPS,.wps,.PDF,.pdf,.XLS,.xls,.XLSX,.xlsx'"
					/>
				</el-form-item>
				<el-form-item
					label-width="120px"
					prop="captcha"
				>
					<div class="qr-code-box">
						<div class="code-input">
							<el-input
								v-model="opinionForm.captcha"
								placeholder="请输入验证码"
							></el-input>
						</div>
						<div
							class="code-image"
							@click="handleChangeImage"
						>
							<img :src="pictureCode"/>
						</div>
					</div>
				</el-form-item>
			</el-form>
			<div
				slot="footer"
				class="dialog-footer"
			>
				<el-button @click="handleCancle">取 消</el-button>
				<el-button
					type="primary"
					@click="handleOptionSubmit"
				>确 定</el-button>
			</div>
		</el-dialog>
	</div>
</template>
<script>
import {
	OPTION_LIST,
	APPLY_CONFIG,
	FETCH_CAPTCHACODE,
	OPTION_SUBMIT
} from '@/utils/urls.js';
import Empty from '@/components/empty';
import DiyUpload from '@/components/butler/diy_upload';
import pagination from '@/components/pagination.vue';
export default {
	components: {
		Empty,
		DiyUpload,
		pagination
	},
	props: {
		value: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			file: [],
			opinionForm: {
				content: '',
				file: [],
				captcha: ''
			},
			loading: false,
			messageList: [],
			submitVisible: false,
			currendId: 0,
			rules: {
				content: [
					{required: true, message: '请输入用户意见', trigger: 'blur'}
				],
				captcha: [{required: true, message: '请输入验证码', trigger: 'blur'}]
			}
		};
	},

	async asyncData({$axios, query}) {
		let params = {
			page: 1,
			per_page: 10
		};

		if (query.keyword) {
			params.keyword = query.keyword;
		}
		return Promise.all([
			$axios.get(APPLY_CONFIG),
			$axios.get(OPTION_LIST, {params}),
			$axios.get(FETCH_CAPTCHACODE)
		])
			.then(([config, result, capture]) => {
				const data = result || {};

				let messageTypeOptions = config.user_message_source || [];

				// 添加全部选项
				messageTypeOptions.unshift({
					id: '',
					name: '全部'
				});

				return {
					messageList: data.data,
					pagination: {
						total: data.total,
						pageCount: data.current_page,
						pageSize: data.per_page_num,
						totalPage: data.total_page
					},
					messageTypeOptions,
					pictureCode: capture.img,
					captchaKey: capture.key
				};
			})
			.catch(e => {
				console.log(e);
			});
	},

	watch: {
		file(value) {
			this.opinionForm.file = value.filter(
				item => !item.status || item.status === 'success'
			);
		},
		$route() {
			this.fetchMessageList(1);
		}
	},

	computed: {
		params() {
			let params = {};

			params.content = this.opinionForm.content;
			params.captcha = this.opinionForm.captcha;
			params.key = this.captchaKey;
			params.id = this.currendId;
			if (this.file && this.file.length > 0) {
				params.file = this.file.map(item => ({
					name: item.name,
					save_url: item.url
				}));
			}

			return params;
		}
	},
	methods: {
		// 获取消息列表数据
		fetchMessageList(pageId, pageSize) {
			let params = {
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize
			};

			if (this.$route.query.keyword) {
				params.keyword = this.$route.query.keyword;
			}

			this.loading = true;
			this.$axios
				.get(OPTION_LIST, {params: params})
				.then((data = {}) => {
					this.loading = false;
					this.messageList = data.data;
					this.pagination = {
						...this.pagination,
						total: data.total,
						pageCount: data.current_page,
						totalPage: data.total_page
					};
				})
				.catch(error => {
					this.loading = false;
					console.log(error.message);
				});
		},
		// 点击请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE).then(({img, key}) => {
				this.pictureCode = img;
				this.captchaKey = key;
			});
		},
		handleChangeImage() {
			this.fetchPictureCode();
		},
		handleSumitClick(id) {
			this.currendId = id;
			this.submitVisible = true;
			// 需要获取一下验证码
			this.fetchPictureCode();
		},
		// 过滤条件改变
		handleSearch() {
			this.fetchMessageList(1);
		},

		// 提交表单
		handleOptionSubmit() {
			console.log('file', this.file);
			this.$refs['optionForm'].validate(valid => {
				if (valid) {
					if (this.params.file && this.params.file.length > 10) {
						this.$message.error('文件上传最大数量为10');
						return false;
					}
					this.$axios
						.post(OPTION_SUBMIT, this.params)
						.then(() => {
							this.$message.success('提交成功');
							this.handleCloseDialog();
							this.fetchMessageList(1, 10);
						})
						.catch(error => {
							this.$message.error(error.message || '提交失败');
						});
				} else {
					console.log('error submit!!');
					return false;
				}
			});
		},
		handleCloseDialog() {
			this.submitVisible = false;
			this.$refs['optionForm'].resetFields();
		},
		handleCancle() {
			this.handleCloseDialog();
		},
		// 页码改变
		onPageChange(pageCount) {
			this.fetchMessageList(pageCount);
		},
		handleToOptionDetail(id) {
			this.$router.push({
				path: '/butler/butler_components/option_detail',
				query: {
					id,
					type: 'option'
				}
			});
		}
	}
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.collect-content {
  background: #ffffff;
  border: 1px solid rgba(235, 235, 235, 1);
  min-height: 100%;
  width: 100%;
  .el-divider--vertical {
    width: 6px;
    height: 27px;
    background: @primaryColor;
  }
  .top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid @defaultBorderColor;
    .message-type {
      margin-right: 30px;
      display: flex;
      justify-content: space-around;
      align-items: center;
      .tip {
        padding: 0 10px;
        width: 160px;
        font-size: 16px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @borderLine;
      }
    }
  }
  .list-container {
    padding: 20px 30px 30px 30px;
    min-height: 100%;
    .list-item {
      width: 100%;
      border: 1px solid rgba(235, 235, 235, 1);
      border-radius: 5px;
      margin-bottom: 10px;
    }
    .col-left {
      padding: 10px 20px;
      border-right: 1px solid rgba(235, 235, 235, 1);
      .message {
        font-size: 16px;
        font-family: Microsoft YaHei;
        font-weight: bold;
        .item-title {
          padding: 10px 0;
        }
      }
      .title {
        display: flex;
        justify-content: space-between;
        .time {
          font-size: 14px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: @borderLine;
        }
      }
      .el-divider--horizontal {
        margin: 5px 0;
      }
      .describe {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @textColor;
        margin: 10px 0;
        min-height: 40px;
        -webkit-line-clamp: 2;
        // display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    }
    .el-row {
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .col-right {
      text-align: center;
    }
  }
  .dialog {
    .el-dialog__header {
      background: @primaryColor;
      color: #ffffff;
      font-size: 18px;
      text-align: left;
      font-weight: bold;
    }
    .el-icon-close {
      color: #ffffff;
      font-size: 20px;
    }
    .tip {
      color: @defaultTextColor;
      font-weight: bold;
      padding: 20px 20px 0 20px;
    }
  }
  .dialog-footer {
    text-align: left;
    padding: 0px 20px 0 20px;
    .content {
      border: 1px solid @defaultBorderColor;
      padding: 10px;
      margin-top: 10px;
    }
  }
  .tip-box {
    font-size: 14px;
    display: flex;
    justify-content: flex-start;
    padding-top: 10px;
    .tip {
      color: #cbcbcb;
      margin-right: 50px;
    }
    .tip-content {
      color: #818181;
    }
  }
  .pagination {
    text-align: center;
    padding: 20px 0;
    .number {
      width: 40px;
      height: 40px;
      line-height: 40px;
    }
    .btn-prev,
    .btn-next {
      height: 40px;
      border: 1px solid rgba(235, 235, 235, 1);
      background: none;
      padding: 0 10px;
    }
    .more {
      height: 40px;
      line-height: 40px;
    }
  }
  .optionForm {
    .upload-tip {
      color: #818181;
    }
    .qr-code-box {
      display: flex;
      justify-content: space-between;
    }
    .code-input {
      flex: 1;
    }
    .el-input__inner {
      border-radius: 0;
    }
    .code-image {
      width: 150px;
      border: 1px solid #dcdfe6;
      height: 40px;
      margin-left: 30px;
      img {
        width: 100%;
        height: 100%;
      }
    }
    .dialog-footer {
      text-align: center;
    }
  }
}
</style>

