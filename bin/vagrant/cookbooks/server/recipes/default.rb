require_recipe "apt"
require_recipe "vim"
require_recipe "ntp"
require_recipe "mysql"
require_recipe "memcached"
require_recipe "git"
require_recipe "varnish"

require_recipe "apache2"
require_recipe "apache2::mod_php5"
require_recipe "apache2::mod_ssl"

require_recipe "php"
require_recipe "php::module_apc"
require_recipe "php::module_curl"
require_recipe "php::module_gd"
require_recipe "php::module_mysql"

require_recipe "xdebug"

node.set[:xdebug][:remote_enable]       = 1
node.set[:xdebug][:remote_connect_back] = 1
node.set[:xdebug][:remote_log]          = "/tmp/xdebug.log"

node.set["apache"]["user"]  = "vagrant"
node.set["apache"]["group"] = "vagrant"

web_app "rax" do
  server_name    "dev.rax.com"
  server_aliases "*.dev.rax.com"
  docroot        "/home/vagrant/rax/web"
  app_env        "development"
end
