<template>
	<div class="enterprise_info-container">
		<div
			class="empty-box"
			v-if="!enterprise || !enterprise.name"
		>
			<img
				class="empty"
				src="~assets/images/butler/empty.png"
			>
			<p class="text">您尚未进行企业认证，认证后可以查看企业信息。</p>
			<el-button
				type="primary"
				@click="handleToCertification"
			>去企业认证</el-button>
		</div>
		<div v-else>
			<p class="vertical-title-box"><el-divider direction="vertical"></el-divider><span>企业基本信息</span></p>
			<div>
				<div class="explain">
					<img class="small">
					<div class="right">
						<div>
							<img
								class="icon_tips"
								src="~assets/images/butler/icon_Tips.png"
							>
						</div>
						<p>	企业的工商信息用于政策申报，如果企业的工商信息有误，请点击右侧我要咨询反馈具体情况，企业管家收到后会及时进行处理。</p>
					</div>
				</div>
				<div>
					<p class="vertical-title-box"><el-divider direction="vertical"></el-divider><span>企业基本信息</span></p>
					<div class="form-content">
						<table class="form-table">
							<tr>
								<td class="odd">单位名称</td>
								<td class="even">
									{{enterprise.name || '--'}}
								</td>
								<td class="odd">组织机构代码</td>
								<td class="even">
									{{enterprise.organization_code || '--'}}
								</td>
							</tr>
							<tr>
								<td class="odd">统一社会信用代码</td>
								<td class="even">
									{{enterprise.unified_credit_code || '--'}}
								</td>
								<td class="odd">注册时间</td>
								<td class="even">
									{{enterprise.regist_time | formatTime('YYYY-MM-DD')}}
								</td>
							</tr>
							<tr>
								<td class="odd">注册地址</td>
								<td class="even">
									{{enterprise.regist_address || '--'}}
								</td>
								<td class="odd">注册资本(万元)</td>
								<td class="even">
									{{enterprise.regist_capital || '--'}}
								</td>
							</tr>
							<tr>
								<td class="odd">法人代表</td>
								<td class="even">
									{{enterprise.legal_represent || '--'}}
								</td>
								<td class="odd">行业类别</td>
								<td class="even">
									{{enterprise.first_industry_name ? enterprise.first_industry_name : '--'}}{{enterprise.second_industry_name ?  '/' + enterprise.second_industry_name : ''}}{{enterprise.third_industry_name ?  '/' + enterprise.third_industry_name : ''}}{{enterprise.fourth_industry_name ?  '/' + enterprise.fourth_industry_name : ''}}
								</td>
							</tr>
							<tr>
								<td class="odd">经营(办公)面积</td>
								<td class="even">
									{{enterprise.business_area || '--'}}
								</td>
								<td class="odd">纳税人识别号</td>
								<td
									class="even"
								>
									{{enterprise.tax_number  || '--'}}
								</td>
							</tr>
							<tr>

								<td class="odd">经营(办公)地址</td>
								<td
									class="even"
									colspan="3"
								>
									{{enterprise.business_address || '--'}}
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div>
					<p class="vertical-title-box"><el-divider direction="vertical"></el-divider><span>企业管家</span></p>
					<div class="form-content">
						<table class="form-table form-table-center">
							<tr>
								<td class="odd">所属部门</td>

								<td class="odd">姓名</td>

								<td class="odd">联系电话</td>
							</tr>
							<tr>
								<td class="even">
									{{steward.department_name  || '--'}}
								</td>

								<td class="even">{{steward.name  || '--'}}</td>

								<td class="even">	{{steward.mobile  || '--'}}</td>

							</tr>
						</table>
					</div>
				</div>
				<div>
					<p class="vertical-title-box"><el-divider direction="vertical"></el-divider><span>营业执照</span></p>
					<div class="image-content">
						<img
							v-if="enterprise && enterprise.business_license_url"
							class="image"
							:src="enterprise.business_license_url"
						>
						<img
							v-else
							class="image"
							src="~assets/images/butler/license.png"
						>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import {
	USER_ENTERPRISE,

} from '@/utils/urls.js';
export default {
	data() {
		return {
			enterpriseBasicForm: {}
		};
	},
	async asyncData({$axios}) {
		return Promise.all([
			$axios.get(USER_ENTERPRISE),

		])
			.then(([result]) => ({
				enterprise: result.enterprise || {},
				steward: result.steward || {}
			}))
			.catch(e => {
				console.log(e);
			});
	},
	methods: {
		handleToCertification() {
			this.$router.push('/certification');
		}
	}
};
</script>
<style lang="less" scoped>
@import '~assets/css/common_avairail.less';
.enterprise_info-container {
    height: 100%;
  .vertical-title-box {
    margin: 20px 0;
  }
  .el-divider--vertical {
      width: 6px;
      height: 27px;
      background: @primaryColor;
    }
 .explain {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 18px 32px;
      background: @applyItemBgColor;
      border: 1px solid #bcd5e9;

      .right {
        display: flex;
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        color:rgba(0,81,146,1);
        .icon_tips {
          width: 39px;
          height: 39px;
          margin-right: 20px;
        }
      }
    }

    .form-table {
      width: 100%;
      border: 1px solid rgba(220, 223, 230, 1);
      border-collapse: collapse;
      margin-top: 20px;
      margin-bottom: 25px;


      tr td {
        min-height: 40px;
        line-height: 40px;
        border: 1px solid rgba(220, 223, 230, 1);
        text-align: right;
        padding: 0 20px;
      }
      tr td.odd {
        width: 172px;
        background: @applyItemBgColor;
        color: @textColor;
        font-size:14px;
        font-weight:bold;
      }


      tr td.even {
        text-align: left;
      }
    }
    .form-table-center {
      td.odd {
        text-align: center;
      }
      tr td.even {
        text-align: center;
      }
    }
    .image-content {
      display: flex;
      justify-content: center;
    }
    .image {
      width:553px;
      height:391px;
    }
    .empty-box{
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 100px;
    }
    .text {
      padding: 20px 0;
    }
    .empty {
      width: 119px;
      height: 119px;
    }

}

</style>
