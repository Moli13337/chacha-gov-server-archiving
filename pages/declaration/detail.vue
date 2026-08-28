<template>
	<div class="decaration-detail-container">
		<!-- 面包屑 -->
		<!-- <bread-crumb/> -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/declaration">政策申报</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>申报详情</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="content">
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
							@click="handleOnlineDeclaration(detailData.id)"
						>在线申报</el-button>
						<el-button
							type="primary"
							v-if="detailData.policy&& detailData.policy.enc_id"
							@click="handleNoticeDetail(detailData.policy.enc_id)"
						>来源申报通知
						</el-button>
					</div>
				</div>
				<div class="tips">
					<p class="tip-item"><span>适用对象：</span>{{detailData.province_name}}{{detailData.city_name}}{{detailData.district_name}}</p>
					<p class="tip-item"><span>政策类型：</span>{{detailData.mold_name}}</p>
				</div>
				<div class="tips">
					<p class="tip-item"><span>集中申报时间：</span>{{detailData.validity_sdate | formatDate}} 至 {{detailData.validity_edate | formatDate}}</p>
					<p class="tip-item"><span>上传时间：</span>{{detailData.created_at | formatDate}}</p>
				</div>
			</div>

			<el-tabs
				type="border-card"
				class="text-content"
			>
				<el-tab-pane label="项目详情">
					<p class="title"><el-divider direction="vertical"></el-divider>政策依据</p>
					<div
						class="detail-contents"
						v-html="detailData.policy_basis"
					>
					</div>
					<p class="title"><el-divider direction="vertical"></el-divider>支持对象</p>
					<div
						class="detail-contents"
						v-html="detailData.sup_object"
					>
					</div>
					<p class="title"><el-divider direction="vertical"></el-divider>支持标准</p>
					<div
						class="detail-contents"
						v-html="detailData.sup_content"
					>
					</div>
					<p class="title"><el-divider direction="vertical"></el-divider>申报条件</p>
					<div
						class="detail-contents"
						v-html="detailData.apply_condition"
					>
					</div>
					<p class="title"><el-divider direction="vertical"></el-divider>申报材料</p>
					<div class="detail-contents">
						<table class="material-table">
							<thead>
								<tr>
									<th>
										序号
									</th>
									<th>
										材料名称
									</th>
									<th>
										是否必备
									</th>
								</tr>
							</thead>
							<tbody>
								<tr
									v-for="(item, index) in detailData.materials.slice(0, detailData.materials.length-1)"
									:key="index"
								>
									<td class="col-1">
										{{index+1}}
									</td>
									<td>
										{{item.name}}
									</td>
									<td  class="col-3">
										{{item.is_need_name}}
									</td>
								</tr>
								<tr>
									<td class="col-1">
										其它说明
									</td>
									<td
										:colspan="2"
										class="materials_other"
									>
										<!-- {{detailData.materials_other.content}} -->
										<pre class="materials">{{detailData.materials_other.content}}</pre>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<p class="title"><el-divider direction="vertical"></el-divider>政策咨询</p>
					<div
						class="detail-contents"
						v-html="detailData.policy_advisory"
					>
					</div>
					<p class="title"><el-divider direction="vertical"></el-divider>其它内容</p>
					<div class="detail-contents">
						<div v-if="!detailData.plate || detailData.plate.length === 0">
							暂无其它内容
						</div>
						<el-row
							class="plate-item"
							v-for="(item, index) in detailData.plate"
							:key="index"
						>
							<el-col
								:span="2"
								v-if="item.title"
							><div class="title">{{item.title}}: </div></el-col>
							<el-col :span="22"><div
								class="htmlContent"
								v-html="item.content"
							></div></el-col>
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
								v-for="(item, index) in detailData.file"
								:key="index"
							>
								<el-col
									:span="2.8"
									class="file-number"
								>
									附件{{index+1}}:
								</el-col>
								<el-col
									:span="21"
								>
									<a
										style="color: #005192;"
										class="material-url"
										@click="downloadFile(item.save_url)"
									> <i class="el-icon-document"></i>{{item.name}}
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
	DECLARATION_DETAIL,
} from '@/utils/urls.js';
import download from '@/utils/download.js';
import collection from '@/utils/collection.js';
export default {
	mixins: [download, collection],
	scrollToTop: true,
	data() {
		return {
			detailData: {},
			activeName: 'first',
			collection_obj_type: 8
		};
	},
	// 验证ID是否存在
	validate({query}) {
		return query.id !== undefined;
	},
	// 加载完数据才显示页面
	async asyncData({query, $axios}) {
		console.log(query);
		const result = await $axios.get(DECLARATION_DETAIL, {params: {id: query.id}});

		return {
			detailData: result || {},
			collection_enc_id: result.enc_id,
		};
	},
	mounted() {
		this.initIsCollection();
	},
	methods: {
		// 初始化isCollection
		initIsCollection() {
			this.isCollection = this.detailData.collections_count;
		},
		handleClick(tab, event) {
			console.log(tab, event);
		},
		handlePolicyExplain() {
			this.$router.push({name: 'policy-id-explain'});
		},
		objectSpanMethod({rowIndex, columnIndex}) {
			if (columnIndex === 1) {
				if (rowIndex === 2) {
					return {
						rowspan: 1,
						colspan: 2
					};
				}
			}
		},
		handleOnlineDeclaration(id) {
			let routeDate = this.$router.resolve({
				name: 'declaration-online-mode',
				params: {
					mode: 'create'
				},
				query: {id}
			});

			window.open(routeDate.href, '_blank');
		},
		handleNoticeDetail(id) {
			let routeDate =	this.$router.resolve({name: 'notice-declare', query: {id: id}});

			window.open(routeDate.href, '_blank');
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
};
</script>
<style lang="less" >
@import '~assets/css/common_avairail.less';
@import '~assets/css/common.less';
.decaration-detail-container {
  width: 100%;
  background: @backGroundColor;
  box-shadow:0px 0px 5px rgba(0,0,0,0.05);
  padding: 15px;
  .bread-crumb {
    line-height: 18px;
    border-bottom: 1px solid @borderLine;
    padding-bottom: 15px;
    .el-divider {
      width:5px;
      height:18px;
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
          word-break: break-all;
          width: 703px;
          font-size:20px;
          margin-bottom: 20px;
          font-family:Microsoft YaHei;
          font-weight:400;
          color: @primaryColor;
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
          font-size:14px;
          font-family:Microsoft YaHei;
          font-weight:400;
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
      font-family:Microsoft YaHei;
      font-weight:400;
      padding-bottom: 30px;
      .el-tabs__item {
        font-size:16px;
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
      .policy-item {
        line-height: 35px;
        color: @poliyItemColor;
        cursor: pointer;
      }
      }
    }

  }
  .material-table {
    text-align: center;
    border-collapse:collapse;
    width: 100%;
    font-size:16px;
    font-family:Microsoft YaHei;
    font-weight:400;
    color: #818181;
    td, th{
      border: 1px solid #DCDFE6;
      height: 49px;
    }
    th {
      color: #CBCBCB;
    }
    .col-1 {
        width: 120px;
    }
    .col-3 {
       width: 150px;
    }
    .materials_other {
      padding: 0 50px;
      font-family:Microsoft YaHei;
      .materials {
        font-family:Microsoft YaHei;
        text-align: left;
        white-space: pre-wrap; /*css-3*/
        white-space: -moz-pre-wrap; /*Mozilla,since1999*/
        white-space: -pre-wrap; /*Opera4-6*/
        white-space: -o-pre-wrap; /*Opera7*/
        word-wrap: break-word; /*InternetExplorer5.5+*/
      }
    }
  }
  .plate-item {
    padding-bottom: 10px;
    // text-indent:35px;
    display:flex;
    .title {
      color: #CBCBCB;
      padding: 0 !important;
    }
    .htmlContent {
      padding-left: 10px;
    }
  }
  .load {
    padding: 10px 50px;
    color: @poliyItemColor;
  }
}
</style>

