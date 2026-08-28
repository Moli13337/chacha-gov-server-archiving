<template>
	<div class="organ-detail-container">
		<!-- 搜索框 -->
		<div class="search-container">
			<div class="search-box">
				<div class="input-box">
					<el-input
						class="search"
						:placeholder="searchPlaceHolder"
						v-model="searchContent"
					>
						<el-button
							slot="append"
							icon="el-icon-search"
							@click="handleSearch"
						/>
					</el-input>
				</div>
			</div>
		</div>
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/agent">中介服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>机构详情</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="detail-contianer">
			<div>
				<img
					class="detail-image"
					:src="organDetail.file_url"
				/>
			</div>
			<div class="detail-text">
				<div class="organ-name-box">
					<p class="organ-name">{{organDetail.agent_name}}</p>
					<!-- <div
						class="complaint-tip-box"
						@click="handleComplaint"
					>
						<img
							class="icon_complaint"
							src="~assets/images/icon_complaint.png"
						><p>投诉与举报</p>
          </div>-->
					<div class="colecttion-box">
						<el-button
							type="primary"
							v-if="isCollection == 0"
							@click="handleCollection"
							size="mini"
						>收藏</el-button>
						<el-button
							type="primary"
							plain
							v-if="isCollection > 0"
							@click="handleCancelCollection"
							size="mini"
						>取消收藏</el-button>
					</div>
					<div>
						<p
							@click="handleEvaluta(organDetail.id)"
							style="color: #036DB4; font-size:14px; display: flex; align-items: center;"
						>
							<img
								class="icon-edit"
								src="~assets/images/agent-icon-edit.png"
							/>
							评价
						</p>
					</div>
				</div>

				<div class="tip-box tip-box-items">
					<div class="score-box">
						<div class="score">
							<el-rate
								v-model="organDetail.composite_stars"
								disabled
								text-color="#ff9900"
								score-template="{value}"
							></el-rate>
						</div>
						<div style="margin-left: 20px;">综合评分:{{organDetail.composite_stars || 0}}</div>
					</div>
					<div class="score-box">
						<div>企业评分:{{organDetail.enterprise_stars || 0}}</div>
					</div>
					<div class="score-box">
						<div>部门评分:{{organDetail.department_stars || 0}}</div>
					</div>
				</div>
				<div class="tip-box">
					<span class="title">信用状态：</span>
					<span
						class="score-tip"
					>{{organDetail.credit_type_name ? organDetail.credit_type_name : '信用正常'}}</span>
				</div>
				<div class="tip-box">
					<span class="title">机构地址：</span>
					<span>{{organDetail.province_name == organDetail.city_name ? '' : organDetail.province_name}}{{organDetail.city_name}}{{organDetail.district_name}}{{organDetail.address}}</span>
				</div>
				<div class="tip-box">
					<span class="title">联系人：</span>
					<span>{{organDetail.contact_name}}</span>
				</div>
				<div class="tip-box">
					<span class="title">联系电话：</span>
					<span>{{organDetail.contact_phone}}</span>
				</div>
				<div class="tip-box">
					<span class="title">服务事项：</span>
					<span>{{organDetail.service_item}}</span>
				</div>
				<div class="tip-box">
					<span class="title">备注：</span>
					<span>{{organDetail.remark || '无'}}</span>
				</div>
			</div>
		</div>
		<div class="detail-contianer-tab">
			<el-tabs
				v-model="activeName"
				type="border-card"
				@tab-click="handleClick"
			>
				<el-tab-pane
					label="服务项目详情"
					name="first"
				>
					<template>
						<div class="tab-content">
							<p>
								<el-divider direction="vertical"></el-divider>
								<span class="item-title">服务项目详情</span>
							</p>
							<div
								class="tab-text"
								v-html="organDetail.service_detail"
							>
								<p v-if="!organDetail.service_detail">无</p>
							</div>
						</div>
					</template>
				</el-tab-pane>
				<el-tab-pane
					label="企业评价"
					name="second"
				>
					<template>
						<div class="tab-content">
							<p>
								<el-divider direction="vertical"></el-divider>
								<span class="item-title">企业评价</span>
							</p>
							<div class="comment-box">
								<div class="totol-score">
									<div class="totol-score-title">评价星级：</div>
									<el-rate
										v-model="organDetail.enterprise_stars"
										disabled
										show-score
										text-color="#ff9900"
									></el-rate>
								</div>
								<div>
									<p
										@click="handleEvaluta(organDetail.id)"
										style="color: #036DB4; font-size:14px; display: flex; align-items: center;"
									>
										<img
											class="icon-edit"
											src="~assets/images/agent-icon-edit.png"
										/>
										评价
									</p>
								</div>
							</div>
							<el-tabs
								class="evaluateTabs"
								v-model="evaluateActiveName"
								@tab-click="fetchCommentList"
							>
								<el-tab-pane name="score-first">
									<template>
										<span
											slot="label"
										>全部评价（{{organDetail.enterprise_stars_arr && organDetail.enterprise_stars_arr.total}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-second">
									<template>
										<span
											slot="label"
										>五星（{{organDetail.enterprise_stars_arr && organDetail.enterprise_stars_arr.five}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-third">
									<template>
										<span
											slot="label"
										>四星（{{organDetail.enterprise_stars_arr && organDetail.enterprise_stars_arr.four}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-fourth">
									<template>
										<span
											slot="label"
										>三星（{{organDetail.enterprise_stars_arr && organDetail.enterprise_stars_arr.three}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-fiveth">
									<template>
										<span
											slot="label"
										>二星（{{organDetail.enterprise_stars_arr && organDetail.enterprise_stars_arr.two}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-sixth">
									<template>
										<span
											slot="label"
										>一星（{{organDetail.enterprise_stars_arr && organDetail.enterprise_stars_arr.one}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
							</el-tabs>
						</div>
					</template>
				</el-tab-pane>
				<el-tab-pane
					label="部门评价"
					name="third"
				>
					<template>
						<div class="tab-content">
							<p>
								<el-divider direction="vertical"></el-divider>
								<span class="item-title">部门评价</span>
							</p>
							<div class="comment-box">
								<div class="totol-score">
									<div class="totol-score-title">评价星级：</div>
									<el-rate
										v-model="organDetail.department_stars"
										disabled
										show-score
										text-color="#ff9900"
									></el-rate>
								</div>
							</div>
							<el-tabs
								class="evaluateTabs"
								v-model="evaluateActiveName"
								@tab-click="fetchCommentList"
							>
								<el-tab-pane name="score-first">
									<template>
										<span
											slot="label"
										>全部评价（{{organDetail.department_stars_arr && organDetail.department_stars_arr.total}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-second">
									<template>
										<span
											slot="label"
										>五星（{{organDetail.department_stars_arr && organDetail.department_stars_arr.five}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-third">
									<template>
										<span
											slot="label"
										>四星（{{organDetail.department_stars_arr && organDetail.department_stars_arr.four}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-fourth">
									<template>
										<span
											slot="label"
										>三星（{{organDetail.department_stars_arr && organDetail.department_stars_arr.three}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-fiveth">
									<template>
										<span
											slot="label"
										>二星（{{organDetail.department_stars_arr && organDetail.department_stars_arr.two}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
								<el-tab-pane name="score-sixth">
									<template>
										<span
											slot="label"
										>一星（{{organDetail.department_stars_arr && organDetail.department_stars_arr.one}}）</span>
										<RateList :list="agentCommentList"/>
										<Pagination :pagination="pagination"/>
									</template>
								</el-tab-pane>
							</el-tabs>
						</div>
					</template>
				</el-tab-pane>
				<el-tab-pane
					label="信用行为"
					name="fourth"
				>
					<template>
						<div class="tab-content">
							<p>
								<el-divider direction="vertical"></el-divider>
								<span class="item-title">信用行为</span>
							</p>
							<el-table
								class="creditTable"
								:data="creditData"
								border
								style="width: 100%"
								:header-cell-style="{background: '#005192', color: '#fff', 'text-align': 'center', 'font-weight': 'bold'}"
							>
								<el-table-column
									prop="created_at"
									label="记录时间"
									width="120"
								>
									<template slot-scope="scope">{{scope.row.created_at | formatDate}}</template>
								</el-table-column>
								<el-table-column
									prop="credit_type_name"
									label="失信类型"
									width="120"
								></el-table-column>
								<el-table-column
									prop="project_name"
									label="失信项目"
								></el-table-column>
								<el-table-column
									prop="content"
									label="原因说明"
								></el-table-column>
							</el-table>
						</div>
					</template>
					<Pagination :pagination="pagination"/>
				</el-tab-pane>
			</el-tabs>
		</div>
		<el-dialog
			title="用户投诉"
			:visible.sync="complaintFormVisible"
			class="evaluateForm"
		>
			<div style="padding: 0 0 20px 35px">
				<p>尊敬的用户:</p>感谢您给我们提出的宝贵建议，我们会进行严格保密。您的个人信息不会向外公开，请根据您 的实际情况如实填写，如有必要，我们的工作人员会联系您进行线下核实。
			</div>
			<el-form
				:model="complaintForm"
				ref="complaintForm"
				:rules="rules"
				@closed="handleClose('complaintForm')"
			>
				<el-form-item
					label="投诉/举报机构"
					placeholder="请填写机构名称"
					:label-width="formLabelWidth"
					prop="agent_name"
				>
					<el-input
						v-model="complaintForm.agent_name"
						:disabled="true"
					></el-input>
				</el-form-item>
				<el-form-item
					label="投诉/举报内容"
					:label-width="formLabelWidth"
					prop="content"
				>
					<el-input
						v-model="complaintForm.content"
						type="textarea"
						autocomplete="off"
						show-word-limit
						maxlength="300"
						rows="8"
						placeholder="请填写投诉/举报内容"
					></el-input>
				</el-form-item>
				<el-form-item
					:label-width="formLabelWidth"
					prop="captcha"
				>
					<el-row :gutter="50">
						<el-col :span="16">
							<el-input
								v-model="complaintForm.captcha"
								placeholder="请输入验证码"
								autocomplete="off"
							></el-input>
						</el-col>
						<el-col :span="8">
							<img
								:src="capthaPicture.img"
								@click="handleChangeImg"
							/>
						</el-col>
					</el-row>
				</el-form-item>
			</el-form>
			<div
				slot="footer"
				class="dialog-footer"
			>
				<el-button @click="handleCancelSubmite('complaintForm')">取 消</el-button>
				<el-button
					type="primary"
					@click="handleSubmitComplaint('complaintForm')"
				>提 交</el-button>
			</div>
		</el-dialog>
		<comment
			:uncertifiedVisible="uncertifiedVisible"
			:evaluateFormVisible="evaluateFormVisible"
			:agent_id="agent_id"
			:capthaPicture="capthaPicture"
			@changeCapthaPicture="changeCapthaPicture"
			@changeEvaluateFormVisible="changeEvaluateFormVisible"
			@updateCommentList="updateCommentList"
		/>
	</div>
</template>
<script>
import {
	AGENT_DETAIL,
	AGENT_COMMENT_LIST,
	AGENT_CREDIT,
	FETCH_CAPTCHACODE,
	AGENT_COMPLAINT,
	CHECK_CAPTCHA_CODE
} from '@/utils/urls.js';
import storage from '@/utils/storage';
import RateList from '@/components/agent/rate_list';
import Pagination from '@/components/pagination';
import comment from '@/components/agent/comment';
import collection from '@/utils/collection.js';
export default {
	components: {
		RateList,
		Pagination,
		comment
	},
	mixins: [collection],
	data() {
		return {
			uncertifiedVisible: false,
			evaluateFormVisible: false,
			searchPlaceHolder: '搜索服务',
			searchContent: '',
			organDetail: {},
			agentCommentList: [],
			departmentCommentList: [],
			agentCreditList: [],
			complaintFormVisible: false,
			formLabelWidth: '120px',
			complaintForm: {
				agent_name: '',
				agent_id: '',
				content: '',
				captcha: ''
			},
			agent_id: '',
			keyword: '',
			pagination: {
				total: 0,
				pageCount: 1,
				pageSize: 10
			},
			value: 3.7,
			activeName: 'first',
			evaluateActiveName: 'score-first',
			creditData: [],
			capthaPicture: {
				img: '',
				key: ''
			},
			rules: {
				agent_name: [
					{required: true, message: '请填写机构名称', trigger: 'blur'}
				],
				content: [
					{required: true, message: '请填写投诉/举报内容', trigger: 'blur'}
				],
				captha: [
					{required: true, message: '请填写验证码', trigger: 'blur'},
					{validator: this.checkCaptchaCode, trigger: 'change'}
				]
			},
			collection_obj_type: 16,
			collection_enc_id: ''
		};
	},
	methods: {
		// 请求图片验证码
		fetchPictureCode() {
			this.$axios.get(FETCH_CAPTCHACODE).then(({img, key}) => {
				this.capthaPicture.img = img;
				this.capthaPicture.key = key;
				this.complaintForm.key = key;
			});
		},

		// 校验图形验证码
		checkCaptchaCode(rule, val, callback) {
			console.log(val);
			this.$axios
				.get(CHECK_CAPTCHA_CODE, {
					captcha: val,
					key: this.capthaPicture.key
				})
				.then(() => {
					callback();
				})
				.catch(message => {
					callback(new Error(message || '验证码错误!'));
				});
		},

		// 点击变化图形验证码
		handleChangeImg() {
			this.fetchPictureCode();
		},

		// 获取评价列表
		handleClick(selected) {
			let commentParams = {
				user_type: 0,
				agent_id: this.organDetail.id
			};

			if (selected.name == 'second') {
				(commentParams.user_type = 1),
				this.$axios(AGENT_COMMENT_LIST, {params: commentParams})
					.then(res => {
						this.agentCommentList = res.data || [];
						this.pagination.total = res.total || 0;
						this.pagination.pageCount = res.total_page || 0;
						this.pagination.pageSize = res.per_page_num || 0;
					})
					.catch(error => {
						console.log(error);
					});
			} else if (selected.name == 'third') {
				(commentParams.user_type = 2),
				this.$axios(AGENT_COMMENT_LIST, {params: commentParams})
					.then(res => {
						this.agentCommentList = res.data || [];
						this.pagination.total = res.total || 0;
						this.pagination.pageCount = res.total_page || 0;
						this.pagination.pageSize = res.per_page_num || 0;
					})
					.catch(error => {
						console.log(error);
					});
			} else if (selected.name == 'fourth') {
				let creditParams = {
					agent_id: this.organDetail.id,
					user_type: 1,
					stars: ''
				};

				this.$axios(AGENT_CREDIT, {params: creditParams})
					.then(res => {
						this.creditData = res.data || [];
						this.pagination.total = res.total || 0;
						this.pagination.pageCount = res.total_page || 0;
						this.pagination.pageSize = res.per_page_num || 0;
					})
					.catch(error => {
						console.log(error);
					});
			}
		},
		// 搜索机构列表
		handleSearch() {
			let keyword = this.searchContent.trim();

			this.$router.push({
				path: '/agent/evaluation_list',
				query: {
					keyword: keyword
				}
			});
		},
		// 获取不同星级的评价列表
		fetchCommentList() {
			let creditParams = {
				user_type: this.activeName == 'second' ? 1 : 2,
				agent_id: this.organDetail.id
			};

			switch (this.evaluateActiveName) {
				case 'score-first':
					creditParams.stars = '';
					break;
				case 'score-second':
					creditParams.stars = 5;
					break;
				case 'score-third':
					creditParams.stars = 4;
					break;
				case 'score-fourth':
					creditParams.stars = 3;
					break;
				case 'score-fiveth':
					creditParams.stars = 2;
					break;
				case 'score-sixth':
					creditParams.stars = 1;
					break;
				default:
					break;
			}

			this.$axios(AGENT_COMMENT_LIST, {params: creditParams})
				.then(res => {
					this.agentCommentList = res.data || [];
					console.log('agentCommentList', this.agentCommentList);
					this.pagination.total = res.total || 0;
					// this.pagination.pageCount = res.total_page || 0;
					// this.pagination.pageSize = res.per_page_num || 0;
				})
				.catch(error => {
					console.log(error);
				});
		},

		// 表格样式
		headerStyle({row, rowIndex}) {
			if (rowIndex == 0) {
				return 'headerStyle';
			}
		},

		// 获取机构详情
		fetchOrganDetail(id, keyword) {
			let params = {};

			if (id) {
				params.id = id;
			}
			if (keyword) {
				params.keyword = keyword;
			}

			this.$axios(AGENT_DETAIL, {params: params})
				.then(res => {
					this.organDetail = res || [];
					console.log('this.organDetail', this.organDetail);
					this.isCollection = this.organDetail.collections_count;
					// 用于收藏id
					this.collection_enc_id = res.enc_id;
					let type = this.$route.query.type;

					if (type == 'credit') {
						this.activeName = 'fourth';
						let params = {
							name: 'fourth'
						};

						this.handleClick(params);
					}
				})
				.catch(error => {
					console.log(error);
				});
		},

		// 评价登录
		handleEvaluta(id) {
			console.log('evaluateFormVisible', this.evaluateFormVisible);
			this.agent_id = id;
			let user_info = storage.getItem('user_info');

			if (
				user_info &&
        user_info.enterprise &&
        user_info.enterprise.length > 0
			) {
				this.evaluateFormVisible = true;
				this.fetchPictureCode();
			} else {
				this.uncertifiedVisible = true;
			}
		},

		// 举报和投诉
		handleComplaint() {
			console.log('key', this.capthaPicture.key);
			this.complaintForm.agent_name = this.organDetail.agent_name;
			this.complaintForm.agent_id = this.organDetail.id;
			this.complaintFormVisible = true;
		},

		// 提交举报表单
		handleSubmitComplaint(formName) {
			this.$refs[formName].validate(valid => {
				if (valid) {
					this.$axios
						.post(AGENT_COMPLAINT, this.complaintForm)
						.then(() => {
							this.$message.success('操作成功');
							this.handleCancelSubmite(formName);
							this.fetchPictureCode();
						})
						.catch(error => {
							this.$message.error(error.message);
						});
				} else {
					console.log('error submit!!');
					return false;
				}
			});
		},

		// 取消评价
		handleCancelSubmite(formName) {
			this.complaintFormVisible = false;
			this.$refs[formName].resetFields();
		},
		handleClose(formName) {
			this.handleCancelSubmite(formName);
		},
		changeCapthaPicture() {
			this.fetchPictureCode();
		},
		updateCommentList() {
			this.fetchCommentList();
		},
		changeEvaluateFormVisible(val) {
			this.evaluateFormVisible = val;
		}
	},

	mounted() {
		let id = this.$route.query.id;

		this.fetchOrganDetail(id);
		this.fetchPictureCode();
	}
};
</script>
<style lang="less">
@import "~assets/css/common_avairail";
.search-container {
  .search-box {
    padding: 20px 0;
    width: 1162px;
    margin: 0 auto;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    border: 1px solid @backGroundColor;
    background: @backGroundColor;
    .input-box {
      width: 550px;
      margin: 0 auto;
      .search {
        text-align: center;
        .el-input__inner {
          background: none;
          height: 49px;
          border: 1px solid @primaryColor;
          font-family: MicrosoftYaHei-Bold;
          padding-right: 0;
          position: relative;
        }
        .el-button {
          width: 65px;
          font-size: 24px;
          color: @backGroundColor;
          background: @primaryColor;
          border-top-right-radius: 4px;
          border-bottom-right-radius: 4px;
        }
      }
    }
    .search-options {
      width: 550px;
      margin: 0 auto;
      color: #818181;
      font-weight: 500;
      padding: 10px 0;
      font-size: 14px;
      .search-item {
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @textColor;
        cursor: pointer;
        padding-left: 5px;
      }
      .search-item:hover {
        color: @primaryColor;
      }
      .search-item:active {
        color: @primaryColor;
      }
    }
    .el-input-group__append {
      border: 1px solid @primaryColor;
      background: @primaryColor;
    }
  }
  .search-box-bg {
    background: url("~assets//images/search-agent.png");
    background-size: 100% 100%;
  }
}
.organ-detail-container {
  .detail-contianer {
    border: 1px solid #dcdfe6;
    background: #ffffff;
    margin-top: 20px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    .detail-image {
      width: 391px;
      height: 391px;
    }
    .detail-text {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding-left: 20px;
      .organ-name-box {
        display: flex;
        justify-content: space-between;
        .organ-name {
          font-size: 31px;
          font-weight: bold;
          color: rgba(59, 59, 59, 1);
        }
        .complaint-tip-box {
          display: flex;
          align-items: center;
        }
        .icon_complaint {
          width: 14px;
          height: 14px;
          margin-right: 10px;
        }
      }
      .title {
        color: rgba(59, 59, 59, 1);
        font-weight: bold;
      }
      .tip-box-items {
        display: flex;
        justify-content: flex-start;
        p {
          margin-left: 20px;
        }
      }
      .score-tip {
        display: inline-block;
        color: rgba(255, 165, 45, 1);
        background: rgba(255, 245, 225, 1);
        border: 1px solid rgba(255, 165, 45, 1);
        width: 78px;
        height: 28px;
        text-align: center;
        line-height: 28px;
      }
    }
  }
  .breadcrumb-row {
    padding-top: 40px;
  }
  .el-tabs__content {
    min-height: 600px;
  }
  .detail-contianer-tab {
    background: #ffffff;
    margin-top: 20px;
    .el-tabs--border-card {
      box-shadow: none;
    }
    .el-divider--vertical {
      width: 6px;
      height: 27px;
      background: @primaryColor;
    }
    .tab-content {
      padding: 20px;
    }
    .tab-text {
      color: #818181;
      // text-indent: 28px;
      padding-top: 20px;
      table {
        border: 1px solid @borderLine;
        border-collapse: collapse;
        margin-bottom: 20px;

        td {
          border: 1px solid @borderLine;
        }
      }
    }
    .item-title {
      font-size: 19px;
      font-weight: 400;
      color: #3b3b3b;
    }
    .totol-score {
      display: flex;
      align-items: center;
      height: 40px;
      margin: 20px;
      .totol-score-title {
        height: 40px;
        line-height: 60px;
      }
      .el-rate__item .el-rate__icon {
        font-size: 30px !important;
      }
    }
    .evaluateTabs {
      .el-tabs__item {
        text-align: center;
      }
    }
    .creditTable {
      margin-top: 20px;
      .el-table {
        text-align: center;
      }
      .el-table .headerStyle {
        background: @primaryColor;
      }
    }
  }
  .score-box {
    display: flex;
    justify-content: flex-start;
    margin-right: 20px;
    .el-rate__icon {
      font-size: 25px;
    }
    .score {
      display: flex;
    }
  }
  .comment-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .colecttion-box {
    .el-button--mini {
      border-radius: 0;
    }
  }
}
</style>
