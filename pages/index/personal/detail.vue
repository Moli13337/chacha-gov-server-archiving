<template>
	<div class="decaration-detail-container">
		<!-- 面包屑 -->
		<!-- <bread-crumb/> -->
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/personal">个人中心</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/personal/revised_record">申报资料订正记录</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>订正记录详情</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="content">
			<p class="title"><el-divider direction="vertical"></el-divider>订正记录详情</p>
			<table class="table">
				<tr>
					<td class="col-1">项目名称</td>
					<td
						class="col-2"
						colspan="3"
					>{{detailData.apply.project_name}}</td>
				</tr>
				<tr>
					<td class="col-1">申报企业信息</td>
					<td
						class="col-2"
						colspan="3"
					><span class="table-item">申报单位：{{detailData.apply.enterprise_name}}</span><span class="table-item">申报人：{{detailData.apply.user_name}} </span><span class="table-item">联系方式：{{detailData.user.mobile}}</span></td>
				</tr>
				<tr>
					<td class="col-1">资料订正发起部门</td>
					<td
						class="col-2"
						colspan="3"
					><span class="table-item">部门：{{detailData.department.name}}</span><span class="table-item">操作人：{{detailData.operator_staff.name}} </span><span class="table-item">联系电话：{{detailData.operator_staff.mobile}}</span></td>
				</tr>
				<tr>
					<td class="col-1">编号</td>
					<td class="col-2">{{detailData.id || '无'}}</td>
					<td class="col-1">资料订正状态</td>
					<td class="col-2">{{detailData.status_name || '无'}}</td>
				</tr>
				<tr>
					<td class="col-1">发起时间</td>
					<td class="col-2">{{detailData.created_at | formatDate('YYYY/MM/DD HH:mm:ss')}}</td>
					<td class="col-1">修改时间</td>
					<td class="col-2">{{detailData.updated_at | formatDate('YYYY/MM/DD HH:mm:ss')}}</td>
				</tr>
				<tr>
					<td class="col-1">修改前的文件</td>
					<td class="col-2">
						<el-popover
							placement="bottom"
							title="全部附件"
							width="800"
							trigger="click"
						>
							<template>
								<p
									v-if="!detailData.change_file || !detailData.change_file.length"
									style="color: #005192"
								>暂无修改文件</p>
								<ul v-else>
									<li
										v-for="(item, index) in detailData.change_file"
										:key="index"
										class="file-list-box"
										style="display: flex;justify-content: space-between; align-items: center; padding-bottom: 10px;"
									>
										<div
											style="width: 600px; display: flex; justify-content: space-between; margin-right: 20px;align-items: center;"
										>
											<div 	style="line-height: 20px; flex: 1;"><span>附件{{index+1}}:</span> {{item.file_name}}</div>
											<div 	style="line-height: 20px; width: 220px; text-align: center">{{item.created_at | formatDate('YYYY/MM/DD HH:mm:ss')}}</div>
										</div>
										<div style="flex: 1; display: flex; justify-content: center; align-items: center;">
											<div>
												<el-tag
													type="success"
													size="mini"
													v-if="item.correct_type_name == '新增'"
												>{{item.correct_type_name}}</el-tag>
												<el-tag
													type="danger"
													size="mini"
													v-else-if="item.correct_type_name == '删除'"
												>{{item.correct_type_name}}
												</el-tag>
												<el-button
													@click="downloadFile(item.file_url)"
													type="text"
													slot="reference"
													style="margin-left: 20px;"
												>下载</el-button>
											</div>
										</div>
									</li>
								</ul>
							</template>
							<el-button
								type="text"
								slot="reference"
							>查看全部</el-button>
						</el-popover>

					</td>
					<td class="col-1">审核状态</td>
					<td class="col-2">{{detailData.status_name || '无'}}</td>
				</tr>
				<tr>
					<td class="col-1">审核部门</td>
					<td
						class="col-2"
						colspan="3"
					>
						<span class="table-item">部门：{{(detailData.audit_department && detailData.audit_department.name) || '无'}}</span>
						<span class="table-item">审核人：{{(detailData.audit_staff && detailData.audit_staff.name) || '无'}}</span>
						<span class="table-item">审核时间：{{detailData.audit_time | formatDate('YYYY/MM/DD HH:mm:ss')}}</span>
					</td>
				</tr>
				<tr>
					<td class="col-1">修改内容</td>
					<td
						class="col-2"
						colspan="3"
					>
						<p v-if="!detailData.change_content || !detailData.change_content.length">无</p>
						<p
							v-else
							v-for="(item, index) in  detailData.change_content"
							:key="index"
						>
							{{item}}
						</p>

					</td>
				</tr>
				<tr>
					<td class="col-1">订正无效原因</td>
					<td
						class="col-2"
						colspan="3"
					>{{detailData.mark || '无'}}</td>
				</tr>
			</table>
		</div>
		<div class="back">
			<el-button
				type="primary"
				@click="handleBack"
			>返回</el-button>
		</div>
	</div>
</template>
<script>
import {
	fileDownLoad
} from '@/utils/http';
import {
	FETCH_COREECT_DETAIL,
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
		};
	},
	// 验证ID是否存在
	validate({query}) {
		return query.id !== undefined;
	},
	// 加载完数据才显示页面
	async asyncData({query, $axios}) {
		console.log(query);
		const result = await $axios.get(FETCH_COREECT_DETAIL, {params: {id: query.id}});

		return {
			detailData: result || {},
		};
	},
	methods: {
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
		// 返回上一级
		handleBack() {
			this.$router.go(-1);
		}
	}
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
    .title {
      padding-bottom: 20px;
      .el-divider {
        width: 5px;
        height: 25px;
        background-color: @primaryColor;
      }
    }
    .table {
      width: 100%;
       border:1px solid rgba(220,223,230,1);
       border-collapse: collapse;
       td {
          border:1px solid rgba(220,223,230,1);
           padding: 10px 20px;
       }
      .col-1 {
        width: 200px;
        background: #F9FBFC;
        text-align: right;
      }
      .col-2 {
        .table-item {
          padding-right: 40px;
        }
      }
    }
  }
  .back {
    .el-button {
      border-radius: 0;
      margin-top: 20px;
    }
  }

}
</style>

