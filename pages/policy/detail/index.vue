<template>
	<div class="policy-detail-container">
		<!-- 面包屑 -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/policy">政策查询</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>政策详情</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<empty
			tip="该政策已取消发布"
			v-if="!data || !data.name"
		/>
		<div
			class="content"
			v-else
		>
			<div class="header">
				<div class="title-box">
					<p class="title">{{data.name}}</p>
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
							v-if="isShowApplyNoticeBtn"
							type="primary"
							@click="handleApplyClick"
						>查看申报通知</el-button>
						<el-button
							v-if="isShowExplainBtn"
							type="primary"
							@click="handleExplainClick"
						>政策解读</el-button>
					</div>
				</div>
				<div class="tips">
					<div
						class="tip-item"
						v-if="suitableIndustry && suitableIndustry.length > 0"
					>
						<span>适用行业：</span>
						<p
							v-for="(item , index) in suitableIndustry"
							:key="index"
						>{{item}}</p>
					</div>
					<p
						class="tip-item"
						v-if="suitableDistrict"
					>
						<span>适用地区：</span>
						{{suitableDistrict}}
					</p>
				</div>
				<div class="tips">
					<p
						class="tip-item"
						v-if="validityDate"
					>
						<span>有效期限：</span>
						{{validityDate}}
					</p>
					<p
						class="tip-item"
					>
						<span>发文体系：</span>
						<span>{{publishTypeList[0]}}</span>
					</p>
				</div>
			</div>
			<el-tabs
				type="border-card"
				class="text-content"
				v-model="activeName"
			>
				<!-- <el-tab-pane
					label="政策概述"
					name="summarize"
				>
					<p class="title">
						<el-divider direction="vertical"></el-divider>政策概述
					</p>
					<div
						v-if="!data.summarize || data.summarize.length == 0"
						class="detail-contents"
					>
						暂无相关政策概述
					</div>
					<div
						class="detail-contents summary-content"
						v-else
					>
						<div
							v-for="(item, index) in data.summarize"
							:key="index"
							class="text-content"
						>
							<div class="summary-title">
								<p class="title-text">{{item.name}}</p>
							</div>
							<div
								class="summary-item"
								v-for="(item, index) in item.summarize"
								:key="index"
							>
								<div class="item-title">
									<p
										class="icon-juxing"
									>{{index+1 | toChineseNum}}</p>{{item.title}}
								</div>
								<ul class="list-box">
									<li class="list-item">
										<div class="icon-bofang-box">
											<img
												src="~assets/images/icon-bofang.png"
												class="icon-bofang"
											/>
										</div>
										<p v-html="highlightMoneyAmount(item.content)"></p>
									</li>
								</ul>
							</div>
						</div>
						<div class="image">
							<img
								src="~assets/images/image-policy.png"
								class="image-policy"
							/>
						</div>
					</div>
				</el-tab-pane> -->
				<el-tab-pane
					label="政策详情"
					name="details"
				>
					<p class="title">
						<el-divider direction="vertical"></el-divider>政策详情
					</p>
					<div
						class="detail-contents"
					>
						<pdf
							v-for="i in numPages"
							:key="i"
							:src="src"
							:page="i"
							style="display: inline-block; width: 100%"
						/>
					</div>
				</el-tab-pane>
				<el-tab-pane
					label="相关政策"
					name="relation-policy"
				>
					<p class="title">
						<el-divider direction="vertical"></el-divider>相关政策
					</p>
					<div
						class="detail-contents"
						v-if="(!data.macro_policy_relation || data.macro_policy_relation.length == 0)  && (!data.sup_policy_relation || data.sup_policy_relation.length == 0) && (!data.announce_relation || data.announce_relation.length == 0) && (!data.publicity_relation || data.publicity_relation.length == 0) && (!data.imple_regu_relation || data.imple_regu_relation.length == 0)"
					>
						暂无相关政策
					</div>
					<div
						class="detail-contents"
						v-else
					>
						<!-- 宏观政策 -->
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="data.macro_policy_relation && data.macro_policy_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>
								<span
									class="policy-box-title"
									v-if="data.macro_policy_relation && data.macro_policy_relation.length > 0"
								>宏观政策</span>
							</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in data.macro_policy_relation"
										:key="index"
										@click="handleRelativeClick(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<!-- 扶持政策 -->
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="data.sup_policy_relation && data.sup_policy_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>
								<span class="policy-box-title">扶持政策</span>
							</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in data.sup_policy_relation"
										:key="index"
										@click="handleRelativeClick(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<!-- 申报通知 -->
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="data.announce_relation && data.announce_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>
								<span class="policy-box-title">申报通知</span>
							</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in data.announce_relation"
										:key="index"
										@click="handleRelativeNnnounceClick(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<!-- 公告通知 -->
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="data.publicity_relation && data.publicity_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>
								<span class="policy-box-title">公告通知</span>
							</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in data.publicity_relation"
										:key="index"
										@click="handleRelativeClick(item.id)"
									>{{item.name}}</li>
								</ul>
							</el-col>
						</el-row>
						<!-- 实施细则 -->
						<el-row
							class="policy-box"
							:gutter="50"
							v-if="data.imple_regu_relation && data.imple_regu_relation.length > 0"
						>
							<el-col
								class="policy-type"
								:span="3"
							>
								<span class="policy-box-title">实施细则</span>
							</el-col>
							<el-col
								class="policy-items"
								:span="19"
							>
								<ul>
									<li
										class="policy-item"
										v-for="(item, index) in data.imple_regu_relation"
										:key="index"
										@click="handleRelativeClick(item.id)"
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
						<template v-if="data.file && data.file.length > 0">
							<el-row
								class="policy-box"
								v-for="(item, index) in data.file"
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
import fecha from 'fecha';
import {
	fileDownLoad
} from '@/utils/http';
import {
	QUERY_POLICY_DTAIL
} from '@/utils/urls.js';
import empty from '@/components/empty.vue';
import download from '@/utils/download';
import Pdf from 'vue-pdf';
import CMapReaderFactory from 'vue-pdf/src/CMapReaderFactory.js';
import collection from '@/utils/collection.js';
export default {
	mixins: [download, collection],
	scrollToTop: true,
	components: {
		empty,
		Pdf
	},
	data() {
		return {
			activeName: 'details',
			src: '',
			numPages: 0
		};
	},
	// 验证ID是否存在
	validate({query}) {
		return query.id !== undefined;
	},
	// 加载完政策详情后才显示页面
	async asyncData({query, $axios}) {
		const result = await $axios.get(QUERY_POLICY_DTAIL, {params: {id: query.id}});

		return {
			data: result || {},
			collection_enc_id: result.enc_id,
			collection_obj_type: result.obj_type
		};
	},
	mounted() {
		if (this.data.content_url) {
			this.$nextTick(() => {
				this.src = Pdf.createLoadingTask({
					url: this.data.content_url,
					CMapReaderFactory
				});
				this.src.then(pdf => {
					this.numPages = pdf.numPages;
				});
			});
		}
		this.initIsCollection();
	},
	computed: {
		// 是否显示查看申报通知按钮
		isShowApplyNoticeBtn() {
			// 显示此按钮的政策类型为：宏观政策：1、扶持政策：2、实施细则：3
			let enableObjType = [1, 2, 3].includes(this.data.obj_type);
			// 关联政策中有申报通知
			let haveAnnounceRelation = this.data.announce_relation && this.data.announce_relation.length > 0;

			return haveAnnounceRelation && enableObjType;
		},
		// 是否显示政策解读按钮
		isShowExplainBtn() {
			return this.data.unscramble && this.data.unscramble.id;
		},
		// 适用行业
		suitableIndustry() {
			if (this.data.industry) {
				let industryList = [];
				let industry = '';

				this.data.industry.forEach(item => {
					if (item.first_industry_name) {
						industry = item.first_industry_name;
						if (item.second_industry_name) {
							industry = item.first_industry_name + '-' + item.second_industry_name;
							if (item.third_industry_name) {
								industry = item.first_industry_name + '-' + item.second_industry_name + '-' + item.third_industry_name;
								if (item.fourth_industry_name) {
									industry = item.first_industry_name + '-' + item.second_industry_name + '-' + item.third_industry_name + '-' + item.fourth_industry_name;
								}
							}
						}
					}

					industryList.push(industry);
				});
				return industryList;
			}

			return '不限';
		},
		// 适用地区
		suitableDistrict() {
			// 避免出现undefined
			let province = this.data.province_name || '';
			let city = this.data.city_name || '';
			let district = this.data.district_name || '';

			return province + city + district;
		},
		// 发文体系
		publishTypeList() {
			if (this.data.gov_agen) {
				let publishType = '';
				let publishTypeList = [];

				this.data.gov_agen.forEach(item => {
					publishType =
          item.gov_agen_first_name + '-' + item.gov_agen_second_name + ' ';
					publishTypeList.push(publishType);
				});
				return publishTypeList;
			}
			return '不限';
		},
		// 有效期限
		validityDate() {
			// 后端给的是10位，秒级的时间戳，需要加3个0
			if (this.data.validity_sdate) {
				let startDate = fecha.format(
					Number(this.data.validity_sdate + '000'),
					'YYYY/MM/DD'
				);

				if (this.data.validity_edate) {
					let endDate = fecha.format(
						Number(this.data.validity_edate + '000'),
						'YYYY/MM/DD'
					);

					return startDate + '-' + endDate;
				} else {
					return startDate + '起生效';
				}
			}
			return '';
		},
		// 计算政策概述长度
		summarizeLength() {
			let length = 0;
			let dataLength = this.data.summarize.length;

			this.data.summarize.forEach((item) => {
				length += item.summarize.length;
			});

			return dataLength * length;
		}
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
	methods: {
		// 初始化isCollection
		initIsCollection() {
			this.isCollection = this.data.collections_count;
		},
		// 查看相关申报通知
		handleApplyClick() {
			this.activeName = 'relation-policy';
			console.log(this.activeName);
		},
		handleExplainClick() {
			this.$router.push({
				name: 'policy-detail-explain',
				query: {
					id: this.data.unscramble.id
				}
			});
		},
		handleRelativeNnnounceClick(id) {
			this.$router.push({
				name: 'notice-declare',
				query: {
					id: id
				}
			});
		},
		handleRelativeClick(id) {
			// console.log('相关政策id', id);
			this.$router.push({
				name: 'policy-detail',
				query: {
					id: id
				}
			});
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
	},
	watch: {
		$route() {
			this.$axios.get(QUERY_POLICY_DTAIL, {params: {id: this.$route.query.id}}).then(res => {
				this.data = res;
			}).catch(erro => {
				console.log(erro);
			});
		}
	}
};
</script>
<style lang="less" >
@import "~assets/css/common_avairail.less";
@import '~assets/css/common.less';
.policy-detail-container {
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
    // height: 500px;
    padding: 20px;
    border: 1px solid @borderLine;
    // margin-top: 40px;
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
      position: relative;
      z-index: 999;
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
        min-height: 400px;
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
        .policy-box-title {
          line-height: 35px;
          color: @textColor;
        }
        .policy-item {
          line-height: 35px;
          color: @poliyItemColor;
          cursor: pointer;
        }
      }
    }
    .summary-content {
      min-height: 600px;
      .summary-title {
        font-size: 19px;
        font-family: Microsoft YaHei;
        font-weight: bold;
        .title-text {
          // position: relative;
          z-index: -1;
          width: 650px;
          padding: 0 25px;
          margin: auto;
          height: 39px;
          background: url("~assets/images/bg-title.png");
          background-size: 100% 100%;
          text-align: center;
          color: #ffffff;
          line-height: 39px;
          margin-bottom: 20px;
        }
      }
      .summary-item {
        width: 60%;
        .item-title {
          font-size: 18px;
          font-family: Microsoft YaHei;
          font-weight: bold;
          color: #3895f1;
          display: flex;
          align-items: center;
          .icon-juxing {
            width: 37px;
            height: 37px;
            margin-right: 10px;
            text-align: center;
            background: url("~assets/images/icon-juxing.png");
            background-size: 100% 100%;
            line-height: 37px;
            color: #ffffff;
          }
        }
        .list-box {
          padding: 20px;
          .list-item {
            height: 100%;
            display: flex;
            align-items: flex-start;
            font-size: 16px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #818181;
            padding: 5px 0;
            .icon-bofang {
              width: 8px;
              height: 10px;
              margin-right: 10px;
              margin-top: 5px;
            }
          }
        }
      }
    }
  }
  .image-policy {
    width: 358px;
    width: 358px;
    position: absolute;
    bottom: 0;
    right: 40px;
    z-index: 1;
  }
}
</style>

