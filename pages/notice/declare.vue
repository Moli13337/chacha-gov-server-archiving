<template>
	<div class="notice-declare-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/notice">公示公告</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>申报公示公告</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<empty
			v-if="!detailData || !detailData.name"
			tip="该来源通知已失效"
		>
		</empty>
		<div
			class="content"
			v-else
		>
			<div class="header">
				<div class="title-box">
					<p class="title">{{detailData.name}}</p>
					<div class="btn-box">
						<el-button
							type="primary"
							v-if="isCollection == 0"
							@click="handleCollection"
						>收藏</el-button>
						<el-button
							type="primary"
							plain
							v-if="isCollection > 0"
							@click="handleCancelCollection"
						>取消收藏</el-button>
						<el-button
							type="primary"
							@click="handleExplainClick"
							v-if="detailData.unscramble && detailData.unscramble.length > 0"
						>政策解读</el-button>
					</div>
				</div>
				<div class="tips">
					<p
						class="tip-item"
						v-if="detailData.province_name || detailData.city_name || detailData.district_name"
					>
						<span>适用地区：</span>
						<span>{{detailData.province_name}}{{detailData.city_name}}{{detailData.district_name}}</span>
					</p>
					<p
						class="tip-item"
						v-if="detailData.industry && detailData.industry.length > 0"
					>
						<span>适用行业：</span>
						<span
							v-for="(item, index) in detailData.industry"
							:key="index"
						>{{item}}</span>
					</p>
				</div>
				<div class="tips">
					<p
						class="tip-item"
						v-if="detailData.gov_agen && detailData.gov_agen.length > 0"
					>
						<span>发文体系：</span>
						<span>{{detailData.gov_agen[0].gov_agen_first_name}}-{{detailData.gov_agen[0].gov_agen_second_name}}</span>
					</p>
					<p
						class="tip-item"
						v-if="detailData.pub_time"
					>
						<span>上传时间：</span>
						{{detailData.pub_time | formatDate}}
					</p>
					<p
						class="tip-item"
						v-if="detailData.validity_sdate && detailData.validity_edate"
					>
						<span>集中申报时间：</span>
						{{detailData.validity_sdate | formatDate}} 至 {{detailData.validity_edate | formatDate}}
					</p>
				</div>
			</div>
			<el-tabs
				type="border-card"
				class="text-content"
				v-model="activeName"
			>
				<el-tab-pane
					label="通知详情"
					name="notice-detail"
				>
					<p class="title">
						<el-divider direction="vertical"></el-divider>通知详情
					</p>
					<div class="detail-contents">
						<p v-html="detailData.content"/>
					</div>
				</el-tab-pane>
				<el-tab-pane label="申报项目">
					<p class="title">
						<el-divider direction="vertical"></el-divider>申报项目
					</p>
					<p
						v-if="detailData.project && detailData.project.length == 0"
						style="padding-left: 20px;"
					>
						暂无可申报方向
					</p>
					<div
						class="detail-contents"
						v-if="detailData.project && detailData.project.length > 0"
					>
						<div
							v-for="(item, index) in detailData.project"
							:key="index"
						>
							<el-row
								class="policy-box"
								:gutter="30"
							>
								<el-col
									class="policy-type declare-title"
									:span="2"
								>
									<span class="circle"></span>
								</el-col>
								<el-col
									class="policy-items"
									:span="19"
								>
									<ul
										@click="handleToDeclare(item.id)"
									>
										<li
											class="policy-item"
										>
											{{item.name}}
											<span class="declareing declare-tip">{{item.announce_status_desc}}</span>
										</li>
									</ul>
								</el-col>
							</el-row>
						</div>
					</div>
				</el-tab-pane>
				<el-tab-pane
					label="相关政策"
					name="relation-policy"
				>
					<p class="title">
						<el-divider direction="vertical"></el-divider>相关政策
					</p>
					<p
						v-if="(detailData.publicity_relation && detailData.publicity_relation.length == 0) && (detailData.announce_relation && detailData.announce_relation.length == 0) && (detailData.sup_policy_relation && detailData.sup_policy_relation.length == 0) && (detailData.macro_policy_relation && detailData.macro_policy_relation.length == 0)&& (detailData.sup_policy_relation && detailData.sup_policy_relation.length == 0)"
						style="padding-left: 50px;"
					>
						暂无相关政策
					</p>
					<div
						class="detail-contents"
					>
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="detailData.macro_policy_relation && detailData.macro_policy_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>政策</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in detailData.macro_policy_relation"
										:key="index"
										@click="handleToPolicy(item.id)"
									>{{item.name}}
									</li>
								</ul>
							</el-col>
						</el-row>
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="detailData.sup_policy_relation && detailData.sup_policy_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>扶持政策</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in detailData.sup_policy_relation"
										:key="index"
										@click="handleToPolicy(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="detailData.imple_regu_relation && detailData.imple_regu_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>实施细则</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in detailData.imple_regu_relation"
										:key="index"
										@click="handleToPolicy(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="detailData.announce_relation && detailData.announce_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>申报通知</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in detailData.announce_relation"
										:key="index"
										@click="handelAnnounceRelationClick(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="detailData.publicity_relation && detailData.publicity_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>活动通知</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in detailData.publicity_relation"
										:key="index"
										@click="handelActiveRelationClick(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
					</div>
				</el-tab-pane>
				<el-tab-pane label="资料下载">
					<p class="title">
						<el-divider direction="vertical"></el-divider>资料下载
					</p>
					<div class="detail-contents">
						<template v-if="detailData.file && detailData.file.length > 0">
							<el-row
								class="policy-box"
								v-for="(item, index) in detailData.file"
								:key="index"
							>
								<el-col
									:span="3"
									class="file-number"
								>
									附件{{index+1}}:
								</el-col>
								<el-col
									:span="21"
									style="color: #005192;"
								>
									<a
										class="material-url"
										@click="downloadFile(item.save_url)"
									><i class="el-icon-document"></i>{{item.name}}
									</a>
								</el-col>
							</el-row>
						</template>
						<template v-else>
							<p style="padding-left: 20px;">暂无可下载资料</p>
						</template>
					</div>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</template>
<script>
import {
	fileDownLoad
} from '@/utils/http';
import {
	ANNOUNCE_DETIAL
} from '@/utils/urls.js';
import empty from '@/components/empty';
import download from '@/utils/download.js';
import collection from '@/utils/collection.js';

export default {
	mixins: [download, collection],
	scrollToTop: true,
	components: {
		empty
	},
	data() {
		return {
			activeName: '',
			isCollection: false,
			collection_obj_type: 4,
		};
	},
	async asyncData({query, $axios}) {
		const result = await $axios.get(ANNOUNCE_DETIAL, {params: {id: query.id}}).catch(res => {
			console.log('result', res);
		});

		return {
			detailData: result || {},
			// 用于收藏id
			collection_enc_id: result.enc_id
		};
	},
	methods: {
		// 查看相关申报通知
		handleApplyClick() {
			this.activeName = 'relation-policy';
		},
		handleDownloadClick(url) {
			fileDownLoad(this.$axios, url)
				.then(() => {
					this.$message.success('文件下载成功');
				})
				.catch(error => {
					console.log(error);
					this.$message.error('文件下载失败');
				});
		},
		handleExplainClick() {
			let theUnscramble = this.detailData.unscramble && this.detailData.unscramble[0] || {};

			if (theUnscramble) {
				this.$router.push({
					name: 'policy-detail-explain',
					query: {
						id: theUnscramble.id
					}
				});
			}
		},
		// // 关联政策申报详情
		handleToDeclare(id) {
			console.log(id);
			this.$router.push({name: 'declaration-detail', query: {id: id}});
		},

		// 关联政策详情
		handleToPolicy(id) {
			console.log(id);
			this.$router.push({name: 'policy-detail', query: {id: id}});
		},
		// 关联申报通知
		handelAnnounceRelationClick(id) {
			this.$router.push({name: 'notice-declare', query: {id: id}});
		},
		handelActiveRelationClick(id) {
			this.$router.push({name: 'notice-activity', query: {id: id}});
		},
		// 初始化isCollection
		initIsCollection() {
			this.isCollection = this.detailData.collections_count;
		},
	},
	filters: {
		// 数值转汉字
		toChineseNum(num) {
			let dtext = ['', '十', '百', '千', '万'];
			let len = num.toString().length;
			let numArr = num.toString().split('');
			let numTxt = '';
			const toT = (numIndex) => {
				const arr = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];

				return arr[numIndex];
			};

			for (let i = 1; i <= len; i++) {
				if (len > 5 && i < (len - 3)) {
					if (i == (len - 4)) {
						numTxt += numArr[i - 1] == 0 ? '' : toT(numArr[i - 1]);
						numTxt += '万';
					} else {
						if ((numArr[i - 2] == 0 && numArr[i - 1] == 0) || ((numArr[i - 1] == 0) && (numArr[len - 5] == 0) && numArr[i] == 0)) {
							numTxt += '';
						} else {
							numTxt += (((numArr[i - 1] == 0) && (numArr[len - 5] != 0)) || (numArr[i] != 0 && numArr[i - 1] == 0)) ? '零' : (toT(numArr[i - 1]) + dtext[len - i - 4]);
						}
					}
				} else {
					if ((numArr[i - 1] == 0 && i == len) || (numArr[i] == 0 && numArr[i - 1] == 0)) {
						numTxt += '';
					} else {
						numTxt += numArr[i - 1] == 0 ? '零' : (toT(numArr[i - 1]) + dtext[len - i]);
					}
				}
			}
			return numTxt;
		},
	},
	mounted() {
		this.$nextTick(() => {
			this.activeName = 'notice-detail';
		});
		this.initIsCollection();
	},
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
@import '~assets/css/common.less';
.notice-declare-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width: 5px;
      height: 18px;
      background-color: @primaryColor;
    }
    .bread-crumb-tip {
      display: inline-block;
      height: 18px;
      line-height: 18px;
    }
  }
  .content {
    width: 100%;
    padding: 20px;
    border: 1px solid @borderLine;
    .header {
      .title-box {
        display: flex;
        justify-content: space-between;
        .title {
          font-size: 20px;
          margin-bottom: 20px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: @primaryColor;
          padding-right: 20px;
        }
        .btn-box {
          .el-button {
            width: 109px;
            height: 31px;
            border-radius: 0;
            text-align: center;
            padding: 0;
          }
        }
      }
      .tips {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        display: flex;
        justify-content: flex-start;
        .tip-item {
          margin-right: 40px;
          padding: 5px 0;
          span {
            margin-right: 10px;
          }
        }
      }
    }
    .text-content {
      color: @textColor;
      margin-top: 20px;
      box-shadow: none;
      font-family: Microsoft YaHei;
      font-weight: 400;
      padding-bottom: 30px;
      .el-tabs__content {
        min-height: 400px;
      }
      .el-tabs__item {
        font-size: 16px;
      }
      .title {
        display: flex;
        align-items: center;
        padding: 20px 39px;
        color: @boldTextColor;
        .el-divider--vertical {
          width: 6px;
          height: 27px;
          background: @primaryColor;
          margin-left: 0;
        }
      }
      .detail-contents {
        padding: 0 39px;
        .file-number{
          text-align: center;
        }
				.material-url {
          cursor: pointer;
        }
        table {
        border-collapse: collapse;
        tr,th,td {
          border: 1px solid @borderLine;
        }
      }
      a {
          text-decoration: underline;
          color: @primaryColor;
        }
      }
      .policy-box {
        margin-bottom: 20px;
        .policy-type {
          line-height: 35px;
        }
        .declare-title{
          text-align: center;
            .circle {
              display: inline-block;
              width: 8px;
              height: 8px;
              border: 1px solid @borderLine;
            }
        }
        .policy-item {
          line-height: 35px;
          color: @poliyItemColor;
          cursor: pointer;
          .declare-tip {
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
          }
          .declareing {
            color: @tipsColor;
          }
          .declare-ready {
            color: #818181;
          }
          .declare-complete {
            color: #cbcbcb;
          }
        }
      }
    }
  }
}
</style>

