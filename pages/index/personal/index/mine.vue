<template>
	<el-col :span="19">
		<div class="content">
			<div class="top">
				<p><el-divider direction="vertical"></el-divider><span>我的主页</span></p>
			</div>
			<div class="info-container">
				<div class="person-info">
					<div class="tips">
						<p>1.注册时未进行企业认证的用户可以在此处认证企业，进行企业认证后才能进行政策在线申报。</p>
						<p>2.申报信息是与企业绑定的，不会随着账号变更发生改变。</p>
					</div>
					<div class="item">
						<div class="icon-box"><i class="icon-icon- iconfont"></i></div>
						<div class="info-box">
							<p class="item-name">账号信息</p>
							<div class="item-info">
								<p><span class="label">真实姓名：</span><span>{{name}}</span></p>
								<p><span class="label">手机号码：</span><span>{{mobile}}</span></p>
								<p><span class="label">用户类型：</span><span>{{enterprise.length == 0 ? '个人用户': '企业用户'}}</span></p>
							</div>
						</div>
					</div>
					<el-divider></el-divider>
					<div class="item">
						<div class="icon-box"><i class="icon-qiye iconfont"></i></div>
						<div class="info-box">
							<p class="item-name">认证企业</p>
							<div class="item-info">
								<p v-if="enterprise && enterprise.length === 0 ">当前尚未认证企业</p>
								<p v-else><span class="label">统一社会信用代码：</span><span>{{enterprise[0].unified_credit_code}}</span> <span
									class="label"
									style="margin-left: 30px"
								>法人：</span><span>{{enterprise[0].legal_represent}}</span></p>
							</div>
						</div>
						<div class="btn">
							<el-button
								type="primary"
								v-if="enterprise && enterprise.length === 0"
								@click="handleToCertification"
							>
								去认证企业
							</el-button>
							<el-button
								v-else
							>
								已认证
							</el-button>
						</div>
					</div>
					<el-divider></el-divider>
					<!-- <div class="item">
						<div class="icon-box"><i class="icon-ai-password iconfont"></i></div>
						<div class="info-box">
							<p class="item-name">登录密码</p>
							<div class="item-info">
								<p>建议您定期更换密码，设置安全性高的密码可以使帐号更安全</p>
							</div>
						</div>
						<div class="btn">
							<el-button
								type="primary"
								@click="handleUpdatePassword"
							>
								修改
							</el-button>
						</div>
					</div>
					<el-divider></el-divider> -->
					<div class="item">
						<div class="icon-box"><i class="icon-shouji iconfont"></i></div>
						<div class="info-box">
							<p class="item-name">安全手机{{mobile}}</p>
							<div class="item-info">
								<p>安全手机可以用于登录帐号，重置密码或其他安全验证</p>
							</div>
						</div>
						<div class="btn">
							<el-button
								type="primary"
								@click="handleUpdatePhone"
							>
								更换
							</el-button>
						</div>
					</div>
					<el-divider></el-divider>
					<div class="item">
						<div class="icon-box"><i class="icon-youxiang iconfont"></i></div>
						<div class="info-box">
							<p class="item-name">邮箱{{email}}</p>
							<!-- <div class="item-info">
								<p>绑定邮箱可以用于登录帐号</p>
							</div> -->
						</div>
						<div class="btn">
							<el-button
								type="primary"
								@click="handleUpdateEmail"
							>
								<p v-if="email">更换绑定</p>
								<p v-else>绑定</p>
							</el-button>
						</div>
					</div>
					<el-divider></el-divider>
				</div>
			</div>
		</div>
	</el-col>
</template>
<script>
import {
	FETCH_USER_INFO
} from '@/utils/urls';
import storage from '@/utils/storage';
export default {
	data() {
		return {
			region: '',
			pagination: {
				total: 100,
				pageCount: 0,
				pageSize: 10
			},
		};
	},
	// 请求用户信息
	async asyncData({$axios}) {
		const result = await $axios.get(FETCH_USER_INFO);

		const data = result || {};

		storage.setItem('user_info', data);
		return {
			name: data.name,
			mobile: data.mobile,
			email: data.email,
			password: data.password,
			enterprise: data.enterprise || []
		};
	},
	methods: {
		handleToCertification() {
			this.$router.push('/certification');
		},
		handleUpdatePassword() {
			let routeData = this.$router.resolve({
				name: 'accountSet-updatePassword'
			});

			window.open(routeData.href, '_blank');
		},
		handleUpdatePhone() {
			let routeData = this.$router.resolve({
				name: 'accountSet-updatePhone'
			});

			window.open(routeData.href, '_blank');
		},
		handleUpdateEmail() {
			let routeData = this.$router.resolve({
				name: 'accountSet-updateEmail'
			});

			window.open(routeData.href, '_blank');
		}

	},

};
</script>

<style lang="less">
@import '~assets/css/common_avairail.less';
  .content {
    border:1px solid @defaultBorderColor;
    .el-divider--vertical {
      width: 11px;
      height: 34px;
      background: @primaryColor;
    }
    .top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid @defaultBorderColor;
    }
    .info-container {
        min-height: 400px;
      .person-info {
        padding: 50px;
        font-size:16px;
        font-family:Microsoft YaHei;
        font-weight:400;
        // p {
        //    padding: 10px 0;
        // }
        .tips {
          font-size: 16px;
          border: 1px solid @borderLine;
          padding: 10px;
          color: @textColor;
          margin-bottom: 10px;
        }
        .item {
          display: flex;
          justify-content: flex-start;
          align-items: center;
          padding-top: 10px;
          .icon-box {
            width: 80px;
            text-align: center;
          }
          .info-box {
            flex: 1;
          }
          .iconfont {
            font-size: 40px;
            color: @primaryColor;
            // padding: 0 10px;
          }
          .item-name {
            font-weight: 500;
            font-size: 20px;
          }
          .item-info {
            display: flex;
            justify-content: space-between;
            p {
              padding-right: 10px;
            }
          }
        }
        .label {
          color: @textColor;
        }
        .tip {
          font-size:14px;
          font-family:Microsoft YaHei;
          font-weight:400;
          color: @tipsColor;
        }
      }
      .btn {
        text-align: center;
        .el-button {
          border-radius: 0;
        }
      }
    }

  }
</style>

